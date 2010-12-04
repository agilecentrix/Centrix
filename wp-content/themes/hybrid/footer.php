<?php
/**
 * Footer Template
 *
 * The footer template is generally used on every page of your site. Nearly all other
 * templates call it somewhere near the bottom of the file. It is used mostly as a closing
 * wrapper, which is opened with the header.php file. It also executes key functions needed
 * by the theme, child themes, and plugins. 
 *
 * @package Hybrid
 * @subpackage Template
 */
?>
		<?php hybrid_after_container(); // After container hook ?>
    </div>
    </div>
    <div class="footer">
      <ul class="group">
        <li>
          &#194;? Digg Inc. 2010
        </li>
        <li>
          <a href="http://about.digg.com/">
            About Digg
          </a>
        </li>
        <li>
          <a href="http://about.digg.com/ads">
            Advertise
          </a>
        </li>
        <li>
          <a href="http://about.digg.com/partnership">
            Partners
          </a>
        </li>
        <li>
          <a href="http://developers.digg.com/">
            API &amp; Resources
          </a>
        </li>
        <li>
          <a href="http://about.digg.com/blog">
            Blogs
          </a>
        </li>
        <li>
          <a href="http://about.digg.com/contact">
            Contact Us
          </a>
        </li>
        <li>
          <a href="http://jobs.digg.com/">
            Jobs
          </a>
        </li>
        <li>
          <a href="http://about.digg.com/faq">
            Help &amp; FAQ
          </a>
        </li>
        <li>
          <a href="http://about.digg.com/terms-use">
            Terms of Service
          </a>
        </li>
        <li>
          <a href="http://digg.com/topic">
            Topics
          </a>
        </li>
        <li class="last">
          <a href="http://about.digg.com/privacy">
            Privacy
          </a>
        </li>
        <li id="rss-link">
          <a href="http://digg.com/news/recent.rss">
            RSS
          </a>
        </li>
      </ul>
    </div>

</div><!-- #body-container -->

<?php wp_footer(); // WordPress footer hook ?>
<?php hybrid_after_html(); // After HTML hook ?>

</body>
</html>