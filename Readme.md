## CSV Import ##
**Contributors:** Smackcoders

**Donate link:** http://www.smackcoders.com/donate.html

**Tags:** batch, import, plugin, admin, CSV, importer, data, backup, author, automatic, blog, categories, category, statistics, stats, tag, tags, Taxonomy, template, text, thumbnail, thumbnails, title, upload, URL, WordPress.

**Requires at least:** 4.3

**Tested up to:** 4.3

**Stable tag:** 1.0

**Version:** 1.0

**Author:** Smackcoders

**Author URI:** http://profiles.WordPress.org/smackcoders/

**License:** GPLv2 or later

#### Description #### 
An importer for CSV file with inline and featured image handling for Post, Page and Custom Post.
CSV Import enables to import Post, Page and Custom Post along with default WordPress Custom Fields and SEO fields. The SEO fields of All in One SEO plugin and Custom Post Type created with CPT UI plugin can be imported. 

**Highlights**

* Imports both inline and featured image.
* Import and register default custom fields.
* Supports CPT UI plugin to create Custom Post.
* Detailed log with Web view and Admin view.
* supports All in One SEO plugin for SEO fields import.

**Menu**

The plugin has five menus. Dashboard, Post, Page, Custom Post and Settings. The Dashboard holds two graphical representation of import history. The Post, Page and Custom Post menus enables to carry out the import process. The php.ini details in Settings menu enables to cross check the minimum requirement with server configuration. 

**Procedure**

Step-1 Upload <br />

* Upload the CSV to be imported. <br />
* Click on 'Next' to proceed import. <br />

Step-2 Mapping <br />

* The WP fields and CSV headers are listed, map the WP fields with the corresponding header. <br />
* If the import module is Custom Post, choose the custom post type in the dropdown. <br />
* The SEO fields is displayed when All in One SEO is activated in plugin list.

Step -3 Security and Performance <br />

* To eliminate duplicate content, choose the duplicate content and title option. <br />
* Specify the number of server requests based on the server configurations. <br />
* Click on 'Import now' to proceed import. <br />
* Now the log for the current import is generated with both Admin view and Web view.

 

For Support and Feature request, visit <a href="https://smackcoders.freshdesk.com" target="_blank"> Smackcoders support </a>

#### Installation ####

1. Extract the csv-import.zip in wordpress/wp-content/plugins using FTP or through plugin install in wp-admin.
2. Create folder named “uploads”  within wp-content.
3. Give 755 permission for both wp-content and uploads folder. (i.e)
	In terminal run the command, chmod 755 -R wp-content
4. Activate the plugin in WordPress plugin list.

#### Screenshots ####

1. [Dashboard view of the import CSV details.](https://github.com/Smackcoders/WordPress-CSV-Import/blob/master/screenshot-1.png).
2. [CSV Upload section.](https://github.com/Smackcoders/WordPress-CSV-Import/blob/master/screenshot-2.png).
3. [Mapping section with Custom Post type dropdown.](https://github.com/Smackcoders/WordPress-CSV-Import/blob/master/screenshot-3.png).
4. [Detailed log with Admin view and Web view.](https://github.com/Smackcoders/WordPress-CSV-Import/blob/master/screenshot-4.png).
5. [Settings with php.ini details.](https://github.com/Smackcoders/WordPress-CSV-Import/blob/master/screenshot-5.png).

#### Frequently Asked Questions ####

1. Is there any limitation on file size?
No, there is no limitation on file size. The file can have any number of records and the performance is based on the server configurations.

2. Can we register any number of Custom Fields during import?
yes, any number of fields can be registered on the flow of import process.

3. Can we include external URL for image import?
Featured image can be imported from external URL but inline image can be populated only through shortcode.

4. Is there any format specification for the image folder?
Yes, the image folder need to be in zip format and it should contain all the images specified in the shortcode. 

5. How to enable the import button?
The import button will be enabled after uploading the CSV file to be imported. If not enabled, verify whether the file is properly formatted.

6. Can we import into Custom Post Type created with any third party plugin?
No, Custom Post Type created with WordPress function and CPT UI plugin alone is supported.



