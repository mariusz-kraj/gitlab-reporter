# Gitlab.com CI reporter

## Instalation

1. Install from composer
```
composer require --dev mariusz-kraj/gitlab-reporter
```

2. Add config file "gitlab-ci-reporter.yml" in the root of your project:

```yaml
reporters:
    phpunit:
        path: 'tests/_output/report.xml'
    phpcs:
        path: 'build/phpmd.xml'
    phpmd:
        path: 'build/phpcs.xml'
```

3. Add 'after_' to your "gitlab-ci.yml":

```yaml
after_script:
    - bin/gitlab-reporter publish
```

4. Generate access code and add [secret variable](https://docs.gitlab.com/ee/ci/variables/#secret-variables)

    1. Go to [access token](https://gitlab.com/profile/personal_access_tokens)
    2. Give access to API
    3. Copy token
    4. Go to the Project > Settings > CI\CD > Secret Variables
    5. Create _ACCESS TOKEN_ variable with the token as a value
    
5. Create a new merge request and wait for comments!
    

## To Do

* Tests
* Documentation
* Config validation
* PHPUnit Coverage reports
* Comments with attachments
* Changing state of merge request depending on the reports
* Instructions for contributors

## Limitations

* Gitlab CI doesn't have environmental variable with merge request id,library is searching for MR from current branch. If there would be more than one MR from one branch (why?) it will take the first one and ignore rest
