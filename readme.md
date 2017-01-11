How to install this module:

Step1: Copy `userAccounting` directory in your modules folder.

Step2: Add flowing code into your `bootstrap.php` file in your project.
```
Yii::setAlias('userAccounting', 'PATH_TO_MODULE_DIRECTORY/userAccounting');
```

Step3: Add flowing lines in your application admin config:

```
'userAccounting' => [
    'class' => 'userAccounting\UserAccounting',
    'controllerNamespace' => 'userAccounting\controllers\admin',
],
```

Step4: Add flowing lines in your application frontend config:

```
'userAccounting' => [
    'class' => 'userAccounting\UserAccounting',
    'controllerNamespace' => 'userAccounting\controllers\panel',
],
```


Structure of tables and classes:
![alt text](structure.png)
