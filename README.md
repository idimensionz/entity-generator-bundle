# iDimensionz Doctrine Entity Generator
Symfony service and console command that does a simple reverse engineer of MySQL tables into entity classes.

Join the entity-generator channel on [iDimensionz's Community Slack](https://join.slack.com/t/idimensionz-community/shared_invite/enQtNDgyNTI2NTk5MTQwLWJhZTg0MGZiYzU4MWY0YzdmNzViZGNiYTY4MjU1YWYwOGFjYzM4ZGQ0MzFkZDEyYjQ1OTVmNDAxYmQ0Nzk5YjY) to ask questions, share tips and meet others using this software.
## Configuration
* Add the following to registerBundles() in your kernel:
```
    new iDimensionz\EntityGeneratorBundle\EntityGeneratorBundle(),
```
* Add the following to services.yml:
```
     idimensionz.mysql_column_definition_provider:
         class: iDimensionz\EntityGeneratorBundle\Provider\MysqlColumnDefinitionProvider
 
     idimensionz.entity_creator_service:
         class: iDimensionz\EntityGeneratorBundle\Service\EntityCreatorService
         arguments: ["@idimensionz.mysql_column_definition_provider", "@twig"]
 
     idimensionz-entity-generator-command:
         class: iDimensionz\EntityGeneratorBundle\Command\GenerateEntityCommand
         arguments: ["@idimensionz.entity_creator_service", ~]
```
* Add "%kernel.project_dir%/vendor/idimensionz/entity-generator-bundle/templates" to your twig.paths in config.yml. 
* Profit!

## How to Run the Console Command
The console command take 3 parameters:
* schema-name
* table-name
* entity-class-name

Here's an example of how you would create an entity class for the COLUMNS table in the MySQL information_schema database:
```
bin/console idimensionz:generate:entity --schema-name=information_schema --table-name=PROFILING --entity-class-name=Profiling
```

The command will output the class code to the screen where you can copy and paste it into your favorite IDE.

Note: If using PhpStorm, you can easily generate getters and setters with PHP 7 parameter and return value type-hinting as well as doc blocks. 
## To-Do
We'd like to implement the following improvements. Feel free to submit a PR if you want to help out with these.
1. Add twig blocks to the template to make it easier to customize certain sections.
1. Add the ability to specify a bundle where the entity file would be created.