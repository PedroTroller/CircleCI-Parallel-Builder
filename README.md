## CircleCI-Parallel-Builder
*Easy way to build parallel sets of tests for Circle CI*

### Pitch
This tool will create test suites for you. It's made for circle-ci but you can use it with other tools.

### Installation
```bash
composer require pedrotroller/circle-parallel-tests-builder --dev
```
A binary file (`circle-tests`) have been added to your binary directory.

### Configuration
You just have to create a file named `circle-tests.yml` into your project root directory and follow this pattern : 
```yml
"<my_test>": <weigth>
"<my_test>": <weigth>
"<my_test>": <weigth>
```
Where `my_test` is the command to execute, and `weigth` is a number representing the weigth of the execution of your command (can be the duration for exemple).

### Usage
You can display suites by using the following command : 
```bash
./bin/circle-tests display -t 3 # Will split your tests into 3 suites
```
You can launch a suite by using the following command : 
```bash
./bin/circle-tests -i 0 -t 3 # -i represent the index (0 based) of the desired suite
```

### Circle-CI use case
```yml
# ./circle-tests.yml
'bin/behat --no-snippets --tags=~disabled --verbose features/api': 21
'bin/behat --no-snippets --tags=~disabled --verbose features/manager': 50
'bin/phpspec run -fpretty --verbose': 1
'bin/install dev demo': 4
'bin/install prod && app/console doctrine:schema:validate -e=prod': 2
```
```yml
# ./circle.yml
general: # ...

machine: # ...

dependencies: # ...

test:
    override:
        - bin/circle-tests run --index=$CIRCLE_NODE_INDEX --total=$CIRCLE_NODE_TOTAL:
            parallel: true
```