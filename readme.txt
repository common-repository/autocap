=== AutoCap ===
Contributors: nyousefi
Tags: caption, image, image caption
Requires at least: 1.5
Tested up to: 2.8
Stable tag: 0.94

Automatically generates a caption for images based on the IMG title attribute.

== Installation ==

1. Download and unzip.
2. Copy the `autocap` folder to your `plugins` directory.
3. Activate in the admin panel.
4. Enjoy

== Usage ==

The plugin converts any IMG tag with a non-empty title attribute into a caption block, with the text of the title attribute becoming the caption. In addition, the plugin will extract the alignment of the caption that WordPress usually adds and assign that to the caption block. 

So:

`<img src="file.jpg" alt="name" title="This will be the caption." width="400" class="alignleft">`

will become:

`<div class="autocap alignleft" style="width: 400px;">
    <div>
        <img src="file.jpg" alt="name" title="This will be the caption." width="400">
        <p class="autocap-text">This will be the caption.</p>
    </div>
</div>`

The plugin has a Admin options panel that let's you set the start date for the plugin's activity. Any posts you've made after that start date will be affected by the plugin, and posts made before that date will not be. This allows you to easily maintain backwards compatibility with posts you've already made, while captioning new posts after you add the plugin.

Leaving the date field blank will result in all posts being captioned.

You can more finely control what is captioned by adding "nocap" or "autocap" to the IMG class attribute. The "nocap" class attribute will suppress an image from being captioned, even if it has a the title attribute set. The "autocap" class attribute works in conjunction with the *Restrict Captioning* option in the Admin panel. When *Restrict Captioning* is set the plugin requires the "autocap" class attribute to trigger the captioning.

== Frequently Asked Questions ==

= What if I don't want my image to be turned into a caption?  =

Just omit the title attribute.

= Will this plugin interfere with WordPress's own captions? =

No. Just be aware that if the image has a title then it will be affected by the plugin.

= I've just added this plugin and it's messed up all the images on my site. What gives? =

By default the plugin works on all your posts, and you've probably been using the IMG title attribute in your old posts. Just set the plugin start date to a date after your old posts and your new ones will be the only one's affected by the plugin.

= I don't like the way the captions look. How can I change that? =

The plugin folder contains the CSS file for the captions. Modify as you'd like. I've included an image folder there as well, so you can put any background images you want in there, too.

= How do I keep an image from getting captioned? =

If you want to prevent an image from being auto-captioned you can either leave off the title attribute empty, or you can add "nocap" to the image's class attribute. The "nocap" control allows you to give the IMG a title without it being captioned.

= What if I only want certain images to be captioned? =

You can set the Restrict Captioning option in the AutoCap Admin panel. After that only images with an "autocap" class attribute will be captioned. Note that the "autocap" attribute will be removed from the IMG by the plugin to prevent class name conflicts (if you want to use your own CSS you can always assume that "autocap" will be the DIV that wraps the output).

= What happens if I use "nocap" and "autocap" at the same time? =

"nocap" wins.

== Screenshots ==

1. Shows the plugin in action, with alignright and alignleft being used.

== Release Notes ==
0.94 - Added support for "nocap" and "autocap" class attributes on images for finer control of captioning. (based on a suggestion by Blair Mitchelmore)
0.93 - Fixed bug that prevented parenthesis from being used in the captions.
0.90 - Beta