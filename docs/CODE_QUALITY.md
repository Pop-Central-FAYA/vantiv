# Code Quality Tools

As good developers, we should always strive to make sure we right quality code. The tools below will help to work on the codebase to help ensure that our code is written in a maintainable and sustainable way.

The link below are some good resources on code quality tools.

(https://thevaluable.dev/code-quality-check-tools-php/) and (https://phpqa.io/index.html)

## PHPLOC/PHPMetrics

[phploc](https://github.com/sebastianbergmann/phploc) This tool will help to figure out the lines of code in our program and some basic stats around complexity. The command below will run phploc.

```bash
./vendor/bin/phploc app/ |& tee docs/phploc.out
```

[phpmetrics](https://github.com/phpmetrics/PhpMetrics) This tool is similar to phploc, only it presents its output in a more readable format, with some more details. The command below will run phpmetris.

```bash
./vendor/bin/phpmetrics --report-html=docs/phpmetrics app/
```

It is preferable to use PHP Metrics, as it gives a lot of information.

## PHPMD

[phpmd](https://github.com/phpmd/phpmd) This tool helps to detect mess in code, for example, it can help detect the following:

* Possible bugs
* Suboptimal code
* Overcomplicated expressions
* Unused parameters, methods, properties

Run it with the following commands:

```bash
./vendor/bin/phpmd app/ html cleancode,codesize,design,naming,unusedcode --reportfile docs/phpmd.html
```

Open the results in your browser to check the code

## PHPUnit Code Coverage

We already have phpunit installed, so php unit can be run with the code coverage tool enabled, in order to see what percentage of our code is actually unit tested.

The command below will run phpunit with code coverage enabled.

```bash
./vendor/bin/phpunit --coverage-html ./docs/code-coverage tests/
```

*Note* __PHPUnit code coverage does not work at the moment because xdebug is not installed/enabled.__ We should only install xdebug on non production images