**Find and replace cli command**

Find and replace values in the magento 2 tables for selected columns

**Warnings**

- BETA
- Don't ever use without testing on dev or staging environment first. 

**Usage**
--find
--replace
--table
--columns (comma seperated)
--dry_run (show expected result count)

```php

php bin/magento experius_findreplace:replace -f myoldurl.nl -r tomynewurl.nl -t cms_page -c content 

```