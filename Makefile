all-tests:
	bin/phpunit

code-coverage:
	bin/phpunit --coverage-html=code-coverage-report
