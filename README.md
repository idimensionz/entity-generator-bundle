# iDimensionz Doctrine Entity Generator
Symfony service and console command that does a simple reverse engineer of MySQL tables into entity classes.

To use the console command:
1. Create a new command class in your app that extends iDimensionz\Command\GenerateEntityCommand.
2. Add the iDimensionz template directory to your twig config. 
3. Create a new twig file in your "templates" or "Resources/views" directory that extends entityCreator.html.twig.
4. Profit!

### To-Do
We'd like to implement the following improvements. Feel free to submit a PR if you want to help out with these.
1. Create a bundle so that other Symfony apps can have direct access to the **idimensionz:generate:entity** command and template
without any additional coding.
2. Add twig blocks to the template to make it easier to customize certain sections.