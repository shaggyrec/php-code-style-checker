# PHP code-style checker

Cli Util to check PHP code style.

## Usage 

You can run phar script from this repo 

```bash
code-style-checker.phar --diff 
````

Or you can make

```bash
composer install
```

and use

```bash
./src/code-style-checker.php --diff
```

### Options:

### Mandatory: 

#### One and only one of --src or --diff must be specified.

- --src                 Path to a file or a directory with files to check
- --diff                Check git diff of current brunch and merge-base

### Optional:

- --standard            Standard to check (PSR12, PSR2, Squiz, etc.) OR path to a xml-file with a standard
- --log-to              Path to a file to log to
- --debug               Show internal names of standards rules
