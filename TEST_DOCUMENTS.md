WRITING TEST IN FAYA

WHAT YOU NEED TO KNOW

This documents is to make writing test on faya a seamless action. This also guides users to setting up the test.
The test uses a test database connection which has already been created in the config/database.php and hooked up
to the phpunit.xml. The phpunit.xml file holds all the neccessary enviroment variable for the test enviroment.

DEPENDENCY

Testing the application requires a dump to be ran, this is so because not all the tables are completely captured 
in the model. The dump file will also be checked in to the remote repository.

HOW TO RUN TEST

There are two different ways to go about running the actual test. They are as follows:

1. Running all the tests: To run all the tests we need to run ```./vendor/bin/phpunit```
2. Running a particular test in isolation: This means running a particular method in the test class, this tends to be faster 
   and useful when writing test for a functionality or model, after its successfull, you can then run all the test to ensure 
   nothing breaks. The command to run this is: ```./vendor/bin/phpunit path to the specific class to test```

TEST STRUCTURE

The test is structured in the application in such a way that we have two directories in the test folder, the Unit directory and
the Feature directory. The unit directory is where all the test in the models are written, assume you want to write a test for 
a model (User) belonging to multiple companies. the structure is ```test\Unit\Models\User\UserTest.php``` and the feature directory
houses the test we write for all our services. Assume in our ```pp\service``` directory, we have a folder called ```User``` 
which have a file ```GetUserList.php```, the feature test structure will look like ```test\Feature\User\User\GetFeatureListTest.php```
