<?php

/*
Plugin Name: WP-o-Tag
Version: 1.0
Plugin URI: Plugin URL goes here (e.g. http://yoursite.com/wordpress-plugins/plugin/)
Description: This plugin allows you to manage tags with wpomatic.
Author: Telecom Bretagne
Author URI: http://www.coyotte508.com
*/

class WPOTags {
    var $version;

    /* Constructor */
    function WPOTags() {
        $this->version =  '0.9.100';
        $this->installed = get_option('wpot_version');
        $this->autoprocess = get_option('wpot_processwpoposts');

        global $wpdb;
        $this->db = array(
            "tagList" => $wpdb->prefix."wpotags_nonofficial",
            "pending" => $wpdb->prefix."wpotags_pending",
            "transfers" => $wpdb->prefix."wpotags_transfers"
        );

        /*
         * We specify all the hooks and filters for the plugin
        */
        register_activation_hook(__FILE__, array(&$this,'activate'));
        register_deactivation_hook(__FILE__, array(&$this,'deactivate'));

        /*
         * Admin menu.
        */
        add_action('admin_menu', array(&$this, 'adminMenu'));
        /*
         * Tag creation.
         */
        add_action('created_term', array(&$this, 'tagCreated'));
        /*
         * Tag deletion.
         */
        add_action('delete_term', array(&$this, 'tagDeleted'));
        /*
		 * To filter WP-o-Matic queries, so that we can manage the tags
		 * right when a post is imported by WP-o-Matic
		*/
		add_action('query', array(&$this, 'filterWpoQuery'));
    }


    /**
     * Adds the WP Fast Sort item to menu
     *
     */
    function adminMenu() {
        add_options_page("WP-o-Tag", "WP-o-Tag" , 8, __FILE__, array(&$this, 'admin'));
    }

    /**
     * The admin menu that's displayed.
     * The display of the menu occurs in display.php
     *
     */
    function admin() {
        /* Variable to tell if some tags are to be given an alias by the user on this display */
        $transformInit = false;
        /* We process the menu and then display it */
        if (isset ($_REQUEST['wpot_wpomatic'])) {
			/* The wpomatic form was used */
			if (!isset($_REQUEST['process_wpomatic_posts']))
				$results = array();
			else
				$results = $_REQUEST['process_wpomatic_posts'];

			update_option('wpot_processwpoposts', in_array('new', $results));

			if (in_array('all', $results)) {
				$this->processAllPostsForWpo();
			}
		} else if (isset ($_REQUEST['wpot_pending'])) {
            /* Operation on pending tags for posts */
            switch ($_REQUEST['wpot_bulk_action']) {
                case 'add':
                    $this->createAllTags($_REQUEST['pending_tag']);
                    break;
                case 'ignore':
                    foreach ($_REQUEST['pending_tag'] as $id) {
                        $this->changeTagType($id, 'ignored');
                    }
                    break;
                case 'transform':
                    $arrayToCheck = $_REQUEST['pending_tag'];
                    $transformInit = true;
                    break;
            }
        } else if (isset ($_REQUEST['wpot_ignored'])) {
            /* Operation on pending tags for posts */
            switch ($_REQUEST['wpot_bulk_action']) {
                case 'add':
                    $this->createAllTags($_REQUEST['ignored_tag']);
                    break;
                case 'transform':
                    $arrayToCheck = $_REQUEST['ignored_tag'];
                    $transformInit = true;
                    break;
                case 'delete':
                    foreach ($_REQUEST['ignored_tag'] as $id) {
                        $this->deleteOwnTag($id);
                    }
                    break;
                
            }
        } else if (isset ($_REQUEST['wpot_existing'])) {
            /* Operations on tags already existing */
            switch ($_REQUEST['wpot_bulk_action']) {
                case 'ignore':
                    foreach ($_REQUEST['existing_tag'] as $id) {
                        $this->deleteOfficialTag($id);
                    }
                    break;
                case 'transform':
                    /* The received arrays is full of official ids, we want the ids
                     * corresponding to those that are in our db instead.
                     */
                    $arrayToCheck2 = $_REQUEST['existing_tag'];
                    foreach ($arrayToCheck2 as $officialId) {
                        $assoc = $this->getOwnTagAssociated($officialId);
                        if (!$assoc) {
                            $officialName = get_tag($id)->name;
                            $ownId = $this->insertOwnTag($officialName, 'existing');
                            $this->changeTagReference($ownId, $id);
                            $assoc = $this->getOwnTag($ownId);

                            /* Not all posts may be associated to the tag (in case some were imported and some were there from
                             * the start) so we still associate each post
                             */
                            $associatedPosts  = get_posts(array('numberposts' => -1, 'tag' => $officialName));

                            foreach ($associatedPosts as $post) {
                                $this->linkPostWithOwnTag($post->ID, $ownId);
                            }
                        }

                        $arrayToCheck[] = $assoc->id;
                    }
                    /* Standard */
                    $transformInit = true;
                    break;
            }
        } else if (isset ($_REQUEST['wpot_transform_2'])) {
            $transformedTags = split(',', $_REQUEST['transferred_tags']);
            
            foreach($transformedTags as $id) {
                $transformedName = $_REQUEST["transfer_for_$id"];

                $ownTag = $this->getOwnTag($id);

                if ($transformedName == "")
                    continue;

                if ($transformedName == $ownTag->name)
                    continue;

                $this->transformTag($ownTag, $transformedName);
            }
        } else if (isset ($_REQUEST['wpot_transform'])) {
            /* Operations on transformed tags */
            switch ($_REQUEST['wpot_bulk_action']) {
                case 'ignore':
                    $this->clearPostsFromTransformedTags($_REQUEST['transformed_tag']);

                foreach ($_REQUEST['transformed_tag'] as $id) {
                        $this->changeTagType($id, 'ignored');
                    }
                    break;
                case 'add':
                    $this->clearPostsFromTransformedTags($_REQUEST['transformed_tag']);
                    $this->createAllTags($_REQUEST['transformed_tag']);
                    break;
            }
        }

        include('display.php');

		return true;
	}

    function deleteOfficialTag($id)
    {
        if ( !($ownTag = $this->getOwnTagAssociated($officialId)) ) {
            /* We register the tag we're going to delete, so that we keep track of its associated posts */
            $officialName = get_tag($id)->name;
            $ownId = $this->insertOwnTag($officialName, 'existing');
            $this->changeTagReference($ownId, $id);
            $ownTag = $this->getOwnTag($ownId);
        }

        /* Not all posts may be associated to the tag (in case some were imported and some were there from
         * the start) so we still associate each post
         */
        $associatedPosts  = get_posts(array('numberposts' => -1, 'tag' => $officialName));

        foreach ($associatedPosts as $post) {
            $this->linkPostWithOwnTag($post->ID, $ownId);
        }
        
        wp_delete_term($id, 'post_tag');
    }

    /* For each of the transformed tags, remove the transformed tag from all the associated posts */
    function clearPostsFromTransformedTags($array)
    {
        foreach ($array as $id)
        {
            $tag = $this->getTransformedTag($id);

            wp_delete_term(is_term($tag->transfer_name, 'post_tag'), 'post_tag');
            $posts = $this->getPostsLinkedToTag($id);

            foreach ($posts as $post) {
                /* We get the tags of the post, and remove the transformed tag from them */
                $termObjects = wp_get_post_tags($post, array('name'));
                $terms = array();

                foreach ($termObjects as $object) {
                    $terms[] = $object->name;
                }

                $diff = array_diff($terms, array($tag->transfer_name));
                                
                wp_set_post_tags($post, array_diff($terms, array($tag->transfer_name)), false);
            }

            /* Now that every post has the tag removed, we can remove it from the database too */
            $this->deleteTransformedData($tag->id);
        }
    }

    /**
     * Make an alias of a tag to transform it's name to another one.
     *
     * @param object $ownTag The tag that is ours, as an object retrieved the db
     * @param string $newName The new name the tag should take.
     */
    function transformTag($ownTag, $newName)
    {
        /* First we a do basic thing: delete the tag associated, so that new post won't take the new term */
        if ($ownTag->official_id) {
            /* If we delete a tag corresponding to existing posts, it's better to add those posts */
            $this->deleteOfficialTag($ownTag->official_id);
        }

        /* Then update the database */
        $this->changeTagType($ownTag->id, 'transformed');
        $this->deleteTransformedData($ownTag->id);
        $this->addTransformedData($ownTag->id, $newName);

        /* and change every post associated */
        foreach ($this->getPostsLinkedToTag($ownTag->id) as $postid)  {
            wp_add_post_tags($postid, $newName);
        }
    }

    function deleteTransformedData($id)
    {
        global $wpdb;

        $wpdb->query($wpdb->prepare("DELETE FROM {$this->db['transfers']} WHERE id_mytag=%d", $id));
    }

    function addTransformedData($id, $newName)
    {
        global $wpdb;

        $wpdb->query($wpdb->prepare("INSERT INTO {$this->db['transfers']} (id_mytag, transfer_name) VALUES (%d, %s)", $id, $newName));
    }

    /**
	 * A function that filters query. If the query is one such as inserting a "wpo_propositiontag",
	 * then the function assumes it is WP-o-Matic importing a post from another blog and, if the
	 * right option is set, manages the post and its tags.
	 *
	 * Note that this function especially relies on the internal behavior of WP-o-Matic, and may
	 * not work on future WP-o-Matic versions.
	 *
	 * @brief On wpomatic importing, deals with the tags of the post
	 * @param query the query to filer
	 * @return @c query inchanged
	*/
	function filterWpoQuery($query)
	{
		if (!$this->autoprocess)
			return $query;

		global $wpdb;

		/* Tries to match the query to the one wpomatic uses for inserting the original author / permalink.
			This is the part to change if the plugin doesn't work with future versions of wpomatic.
			When all have been stored for a specific post, variables are reset and the post is processed*/
		if(preg_match("/^INSERT INTO $wpdb->postmeta[ ]*\(post_id,meta_key,meta_value[ ]*\)[ ]*"
					  . "VALUES[ ]*\('?([0-9]+)'?,'wpo_propositiontag','(.*)'\)[ ]*\$/", $query, $matches)) {
			$id = $matches[1];
			$tags = $matches[2];

            $this->processPost($id, $tags);
        }

		return $query;
	}

    /**
     * Puts a tag in our db in the ignored tag list
     *
     * @param int $id The id of the tag that is to be ignored
     */
    function changeTagType($id, $type) {
        global $wpdb;

        $wpdb->query($wpdb->prepare("UPDATE {$this->db['tagList']} SET type=%s WHERE id=%d", $type, $id));
    }

    function changeTagReference($id, $officialId) {
        global $wpdb;

        $sql = $wpdb->prepare("UPDATE {$this->db['tagList']} SET official_id=%d WHERE id=%d", $officialId, $id);
        $wpdb->query($sql);
    }

    /**
     * All the tags in our own database corresponding to those ids are created for real
     *
     * @param array<int> $ids The ids of the tags in our own database
     */
    function createAllTags($ids)
    {
        foreach ($ids as $tagid) {
            /* When a tag is created, wpotags automatically checks pendings tags and does the
             * tag-post association
             */
            wp_create_tag($this->ownTagName($tagid));
        }
    }

    /**
     * @brief Processes all wpomatic posts in order to add their tag to the current database
     */
    function processAllPostsForWpo() {
		@set_time_limit(0);
		/* Retrieves all posts */
		$args = array(
            'post_type' => 'post',
            'numberposts' => -1,
		);

		$post_list = get_posts($args);

		/* Process all posts */
		foreach($post_list as $post)
		{
			$this->processPost($post->ID);
		}
    }

    /**
     * Processes a post in order to see its proposition tags and choose what to do with it.
     *
     * @param int id The ID of the post to process
     */
    function processPost($id, $tags='') {
        global $wpdb;

        /* If the tags has propositions, we add them */
        if ($tags) {
            $props = $tags;
        } else {
            $props = get_post_meta($id, "wpo_propositiontag", true);
        }

        if ($props) {
            $tagList = explode(',', $props);

            foreach ($tagList as $tag) {
                if (strlen($tag) == 0)
                    continue;

                if (is_term($tag, 'post_tag')) {
                    /* Link post to tag */
                    wp_add_post_tags($id, $tag);
                    $this->insertOwnTag($tag, 'existing');
                }
                
                $matchingTag = $this->getOwnTagByName($tag);

                /* Checking if the tag is already in our DB */
                if (!$matchingTag) {
                    /* Then the tag is not there, we can insert it */
                    $tagId = $this->insertOwnTag($tag);
                    /* And we link it to the post */
                    $this->linkPostWithOwnTag($id, $tagId);
                } else {
                    $tagId = $matchingTag->id;
                    /* The tag is already in our db. If it's an "ignored" or "pending" tag,
                     * the post is linked to it. If it's a "transferred" tag, the tag is changed.
                     */
                    $this->linkPostWithOwnTag($id, $matchingTag->id);

                    if ($matchingTag->type == 'tranformed') {
                        wp_add_post_tags($id, $this->getTransformedTag($matchingTag->id)->transfer_name);
                    }
                }

                if ( ($officialId = is_term($tag, 'post_tag')) ) {
                    $this->changeTagReference($tagId, $officialId['term_id']);
                }
            }
        }
    }

    /**
     * Links a post to a pending tag
     *
     * @param int $id The post's ID
     * @param int $tagId The tag's ID
     */
    function linkPostWithOwnTag($id, $tagId) {
        global $wpdb;

        $wpdb->query($wpdb->prepare("INSERT INTO {$this->db['pending']} (id_post, id_mytag) VALUES (%d, %d)", $id, $tagId));
    }

    /**
     * Returns the item corresponding to the given tag, if the tag is in
     * our database structure (pending, alias, banned)
     *
     * @param string $tag The tag
     * @return mixed The object corresponding to the tag
     */
    function getOwnTagByName($tag) {
        global $wpdb;

        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->db['tagList']} WHERE name=%s", $tag));
    }

    /**
     * Returns the item corresponding to the given tag, if the tag is in
     * our database structure (pending, alias, banned)
     *
     * @param int $ownId The id
     * @return mixed The object corresponding to the id
     */
    function getOwnTag($ownId) {
        global $wpdb;

        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->db['tagList']} WHERE id=%d", $ownId));
    }


    /**
     * Returns the name corresponding to a tag
     *
     * @param int $tagId The id of the desired tag
     * @return string The name of the tag
     */
    function ownTagName($tagId) {
        global $wpdb;

        return $wpdb->get_row($wpdb->prepare("SELECT name FROM {$this->db['tagList']} WHERE id=%d", $tagId))->name;
    }

    /**
     * Returns the pending tags list
     *
     * @return mixed The pending tag list
     */
    function getPendingTags() {
        global $wpdb;

        return $wpdb->get_results("SELECT * FROM {$this->db['tagList']} WHERE type='pending' ORDER BY name");
    }

    /**
     * Returns the ignored tags list
     *
     * @return mixed The ignored list
     */
    function getIgnoredTags() {
        global $wpdb;

        return $wpdb->get_results("SELECT * FROM {$this->db['tagList']} WHERE type='ignored' ORDER BY name");
    }

    /**
     * Returns the transferred tags list
     *
     * @return mixed The transferred list
     */
    function getTransformedTags() {
        global $wpdb;

        return $wpdb->get_results("SELECT id, name, transfer_name FROM {$this->db['tagList']}, {$this->db['transfers']}  WHERE type='transformed' and id=id_mytag ORDER BY name");
    }

    /**
     * Returned the transformed tag, i.e. ownId, name, and transfer_name
     *
     * @global object $wpdb Wordpress database
     * @param int $id The $id of the tag
     * @return stdClass  The database object corresponding to the tag
     */
    function getTransformedTag($id) {
        global $wpdb;

        return $wpdb->get_row($wpdb->prepare("SELECT id_mytag, name, transfer_name FROM {$this->db['tagList']}, {$this->db['transfers']}
                                    WHERE type='transformed' and id=%d and id_mytag=%d ORDER BY name", $id, $id));
    }

    /**
     * Returns all the posts associated to the given tag
     *
     * @param int $tagId The id of the pending/ignored tag
     * @return mixed An array containing the different post ids
     */
    function getPostsLinkedToTag($tagId)
    {
        global $wpdb;

        return $wpdb->get_col($wpdb->prepare("SELECT id_post FROM {$this->db['tagList']}, {$this->db['pending']}
            WHERE id_mytag=%d AND id=%d", $tagId, $tagId));
    }

    /**
     * Inserts a tag in our own database and returns the corresponding id.
     *
     * @param string $name The name of the tag to insert
     * @param string $type The status of the tag to insert
     * @return mixed The id of the inserted tag or false on failure
     */
    function insertOwnTag($name, $type='pending') {
        global $wpdb;

        $wpdb->query($wpdb->prepare("INSERT INTO {$this->db['tagList']}(name, type) VALUES (%s, %s)", $name, $type));

        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->db['tagList']} WHERE name=%s", $name))->id;
    }

    function deleteOwnTag($tagId) {
        global $wpdb;
        
        $wpdb->query($wpdb->prepare("DELETE FROM {$this->db['tagList']} WHERE id=%d", $tagId));
    }

    /**
     * Called when a tag is created. If the tag was in our database, processes all posts with that tag as a "non official tag"
     * to add that tag to them
     *
     * @param int $tag_id The id of the tag created
     */
    function tagCreated($tag_id)
    {
        $res = get_tag($tag_id);
        if (!$res)
            return;

        $tagName = $res->name;

        $result = $this->getOwnTagByName($tagName);

        /* Then it's a tag in our tags database */
        if ($result) {
            /*
             * First all the posts with that pending tag are given the new
             * tag
             */
            $ownId = $result->id;

            $associatedPosts = $this->getPostsLinkedToTag($ownId);

            foreach ($associatedPosts as $post) {
                wp_add_post_tags($post,$tagName);
            }

            /*
             * Then we delete the pending tag from the database
             */
            $this->changeTagType($ownId, 'existing');
            $this->changeTagReference($ownId, $tag_id);
        }
    }

    /**
     * Called when a tag is deleted. Just changes the type of the tag to "ignored"
     *
     * @param int $tag_id The id of the tag deleted
     */
    function tagDeleted($tag_id)
    {
        $result = $this->getOwnTagAssociated($tag_id);

        /* Then it's a tag in our tags database */
        if ($result) {
            $ownid = $result->id;
            $this->changeTagType($ownid, 'ignored');
        }
    }

    function getOwnTagAssociated($officialId)
    {
        global $wpdb;

        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->db['tagList']} WHERE official_id=%d", $officialId));
    }

    #Called at the activation of the plugin
    function activate() {
        global $wpdb;

        add_option('wpot_version', $this->version);
        $this->installed = true;

        /* use dbDelta() to create/update tables, this header inculdes it */
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');


        $sql = "CREATE TABLE {$this->db['tagList']} (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            name VARCHAR(50) NOT NULL,
            type ENUM('pending', 'transformed', 'ignored', 'existing'),
            official_id BIGINT(20) DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE (name),
            FOREIGN KEY(official_id) REFERENCES " . $wpdb->prefix . "terms(term_id) ON DELETE CASCADE ON UPDATE CASCADE
        );";

        dbDelta($sql);

        $sql = "CREATE TABLE  {$this->db['pending']}  (
            id_post BIGINT(20) NOT NULL,
            id_mytag BIGINT(20) NOT NULL,
            PRIMARY KEY (id_post, id_mytag),
            FOREIGN KEY (id_post) REFERENCES " . $wpdb->prefix . "posts(ID) ON DELETE CASCADE ON UPDATE CASCADE,
            FOREIGN KEY (id_mytag) REFERENCES {$this->db['tagList']}(id) ON DELETE CASCADE ON UPDATE CASCADE
        );";

        dbDelta($sql);

        $sql = "CREATE TABLE {$this->db['transfers']} (
            id_mytag BIGINT(20) NOT NULL,
            transfer_name VARCHAR(50) NOT NULL,
            PRIMARY KEY (id_mytag),
            FOREIGN KEY (id_mytag) REFERENCES {$this->db['tagList']}(id) ON DELETE CASCADE ON UPDATE CASCADE
        );";

        dbDelta($sql);
    }

    #Called at the deactivation of the plugin
    function deactivate() {
    }
}

$wpotags = & new WPOTags();

?>