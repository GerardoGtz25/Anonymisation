# Anonymization
*Anonimization* is a PHP library that helps you to anonymize your databases in a fast and dynamic way.

Note: The data is permanently modified
## Installation
```
composer require gerardo-gtz25/anonymization
```
## Module for database anonymisation

You only have to call the class in this way:

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use Anonymization\Anonymization\Anonymous as Anonymous;

$a = new Anonymous();
$a->start();
```

The anonymous class constructor accepts two parameters:

* Anonymization Configuration File
* Anonymization Configuration DataBase connection

 However, the constructor uses by default the files that are in the path:
 __vendor/ggomez/anonymization/src/Config__

One of the most important parts of anonymization is the configuration file. In the path above, you will find examples of the 3 allowed formats YML, JSON, PHP.

It is very important to maintain the structure, in this case we have a database that his name is "Test", then in the second part there is the sensitive word section, in this part we are going to describe all word that we want to change, the first word is that one we want to change and the seconds is that one we want to add in the database.

In the first part only the database to be anonymized is indicated, in the second part the sensitive words that could reveal someone's identity are specified and finally, in the last section the necessary settings for the anonymization of the database are inserted.

 ```yml
 ##Database name
 Data_base: Test
 
 ##Insert all sensitive information that you want to change
 KeyWord:
   Psychiatry: XXXX

 ##Only integer numbers
 Counter: 10000

 ##Tables that you want to change
 Tables:
   fos_user:
     alias: fos_user fosu, RandomData rd
     mapping:
       email: fosu.email = |email
       email_canonical: email_canonical = rd.email
       first_name: first_name = rd.firstname
       last_name: last_name =  rd.lastname
       created_by: created_by = |User#quipment.fr
       updated_by: updated_by = |UserJ#
       username: username = |UserL#
       username_canonical: username_canonical = |UserE#
     condition: " WHERE (MOD(fosu.id,1499) +1) = rd.id"
 ```
 There are 3 options for data anonymisation:

 *	Generate data from a given format
   * In this case we use sql code to reuse the information
```yml
tiers:
    alias: tiers t, RandomData rd
    mapping:
     raison_sociale: t.raison_sociale =  CONCAT(t.type, ' ',RIGHT( CONCAT( '00000', CONVERT(t.id, char)),5))
```    
* Generate new data from the RandomData table
  * When executing the library, a temporary table is loaded containing random data that can be used to anonymize complete tables
   ```yml
   tel: t.tel = rd.phone
   ```
* Generate new data
  * If you want to generate a random data, such as an address or phone number, you can do it with the following syntax
  ```yml
  email: email = |email
  ```

In all cases it is recommended to use a condition, especially the following syntax is recommended in cases where you want to use the RandomData table, this to get different results in each line of the table
```yml
condition: ' WHERE (MOD(t.id,1499) +1) = rd.id'
```
