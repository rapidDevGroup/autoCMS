# autoCMS
Automagically create a CMS with classnames and data attributes.

## Goal
The goal of this repository is to make a content management system that automatically scans HTML files, creates json data files, and then allows you to simply edit the text of your site through an admin without the need to know code.

### Steps
1. Create an HTML site and add the class "edit" to heading tags, paragraph tags, span tags, or simply the whole div block.
2. Copy the .htaccess and index.php files into an admin folder.
3. Visit http://yoursite/admin/
4. It will ask you to create a password, then scan your html files and ask you which ones to add to the CMS.
5. autoCMS will rename files to .php (scan for links and rename those as well) and add the appropriate php tags.
    * It will scan and look for edit tags and create a data file structure (JSON).
    * It will then bring you to a basic CMS to edit all your content.
    
## Tags Examples

### Text Tags

```HTML
<h1 class="edit">...</h1>

<p class="edit">...</p>

<div class="edit">...</p>
```

### Image Tags

```HTML
<img src="..." class="edit">
```