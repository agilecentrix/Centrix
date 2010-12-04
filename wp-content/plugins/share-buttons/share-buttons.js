function new_window(url) {
    width_screen = (screen.width-700)/2;
    height_screen = (screen.height-400)/2;
    params = 'menubar=0, toolbar=0, location=0, directories=0, status=0, scrollbars=0, resizable=0, width=700, height=400, left='+width_screen+', top='+height_screen;
    window.open(url,'newwin', params);
}