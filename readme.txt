=== Youtube Control Shortcode ===
Contributors: ardnived, ctlt-dev, ubcdev
Tags: shortcode, youtube, media, video, controls
Requires at least: 3.5
Tested up to: 3.5
Stable tag: 1.0

Adds a shortcode that enables you to embed a youtube video with timestamp controls.


== Usage ==
The plugin is a combination of 3 short codes.

[yc_video] is used to embed the youtube video, and wrap around the other two short codes. It accepts the following attributes:
 * id, this is a required attribute, it corresponds to the youtube video's 11 character identifier. You can find this identifier after "v=" in the youtube url.
 * autoplay, adding this to the list of attributes will make the video start as soon as the page is loaded.
 * autohide, adding this to the list of attributes will cause youtube's UI to automatically hide after the video starts.
 * ratio, this should be provided in a format of x:y where x is the relative width to y, which is the relative height. The default value is 72:44, which is the standard ratio for youtube videos at 720p.
 * theme, this can be either 'light' or 'dark' and changes the colouring of youtube's controls. 'dark' is default.
 * width, the preferred width of for your video. By default it will attempt to give the video 75% of the available space.

[yc_title] will insert a title into the list of controls. It's optional to use this tag, if you don't use any yc_title tags a title will be automatically added at the top of the control list. This code only takes one parameter.
 * It's first and only parameter is the text of the title. Make sure to surround your title with quotations.

[yc_control] will insert a timestamp control into the controls list. This code accepts two unnamed attributes.
 * The first attribute is the time that this control should skip to. It should be in the format 0:00:00. ex. 1:30, 20, 1:29:00
 * The second attribute is the title of this control. Remember to surround it in quotation marks.

== Example ==
[yc_video id="7i-1WGUL4VI" autoplay ratio="4:3"]
  [yc_title "Part One"]
  [yc_control 0:20 "Step 1"]
  [yc_control 0:30 "Step 2"]
  [yc_control 0:56 "Step 3"]
  [yc_title "Part Two"]
  [yc_control 1:20 "Step 4"]
[/yc_video]


== Installation ==

1. Extract the zip file into wp-content/plugins/ in your WordPress installation
2. Go to plugins page to activate
3. Use shortcode


== Changelog ==
= 1.0 =
* Initial release
