Use a seeder,

See this for the seeding stuff: https://github.com/orangehill/iseeds

## Code Style

[PHP Codesniffer](https://github.com/squizlabs/PHP_CodeSniffer/wiki) has been installed using composer to check that code written conforms to the [PSR-2](https://www.php-fig.org/psr/psr-2/) style guide.

Try to make sure your code comforms to the style guide, and run php sniffer against the files you are changing to make sure that there are no style guide violations.

The code to run php sniffer is in the project makefile, so to run it, you can just do the following:

```bash
product=vantage make code-sniff
```
`product` in this case allows the makefile to exec into the appropriate container to run php in there, so it really does not matter. You can use torch or vantage if you want.

The results of the sniffing is output into the terminal by default, but you can choose to have the output redirected into a file for further perusal by running the following command.

```bash
product=vantage make code-sniff --report-file=phpcs.out
```

### Automatically fixing violations

There is another tool that can help to fix simple code style violations. The report generated by phpcs gives suggestions of the errors that can be automatically fixed, so after running phpcs and seeing which violations there are and which ones can be fixed automatically, you can run the following to perform the fix