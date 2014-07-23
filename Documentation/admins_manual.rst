=============	
Admins manual
=============


Set up
======



Set a different media server
-----------------------------

If you want to use a different MAdeK server than `medienarchiv.zhdk.ch <http://medienarchiv.zhdk.ch/>`_ you must define it in the **extension configuration**. If you haven't a MAdeK server yet, then you can install it on your own server. 

For more information: `MAdeK installation and administatration guide <https://github.com/zhdk/madek/wiki/Madek-Admin-Guide>`_



Set the number of images for a gallery
---------------------------------------

In the **extension configuration** you can set the range of images.


Constants
---------

+-----------------------------------------------------+----------------------------------------------------------+------------------------------------------------------+
| Constants                                           | Is for:                                                  | Default values                                       |
+=====================================================+==========================================================+======================================================+
| plugin.tx_zhdkmadekplayer_pi1.templateFile          | Structs the whole MAdeK Player                           | EXT:zhdk_madekplayer/res/html/template.html          |
+-----------------------------------------------------+----------------------------------------------------------+------------------------------------------------------+
| plugin.tx_zhdkmadekplayer_pi1.cssFile               | Sets the style of the MAdeK Player                       | EXT:zhdk_madekplayer/res/css/zhdkmadekplayer.css     |
+-----------------------------------------------------+----------------------------------------------------------+------------------------------------------------------+
| plugin.tx_zhdkmadekplayer_pi1.templateFileCaption   | Formats the captions which are set in the image settings | EXT:zhdk_madekplayer/res/html/template_caption.html  |
+-----------------------------------------------------+----------------------------------------------------------+------------------------------------------------------+


============


Formats
=======

Here you can set the format of the player.

Fields
------


+------+-----------------------------+-----------------------------------------+--------------+
| Nr\. | Selection                   | What it does                            | Default value|
+======+=============================+=========================================+==============+
| 1\.  | Player width (pixel)        | Set the width of the player             | 630px        |
+------+-----------------------------+-----------------------------------------+--------------+
| 2\.  | Max. image width (pixel)    | Set the maximum width of the images     | 620px        |
+------+-----------------------------+-----------------------------------------+--------------+
| 3\.  | Max. image height (pixel)   | Set the maximum height of the images    | 500px        |
+------+-----------------------------+-----------------------------------------+--------------+
| 4\.  | No. of thumbnails per page  | Set the number of thumbnails per page   | 5            |
+------+-----------------------------+-----------------------------------------+--------------+
| 5\.  | Background color            | Set the color of the players background | #eeeeee      |
+------+-----------------------------+-----------------------------------------+--------------+
| 6\.  | Border color                | Set the color of the players border     | #dedede      |
+------+-----------------------------+-----------------------------------------+--------------+







