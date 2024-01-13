![image](https://github.com/aatifbangash/Eloquent-ORM-editor/assets/35859555/a498d08a-a042-4ec4-b514-f87152fd7b82)

Copy the file `query_editor.php` to `routes/` directory and add the following block of code at the end of the `web.php` file.
```php
if(App::environment('local')) {
    require __DIR__.'/query_editor.php';
}
```
And also copy the `orm.blade.php` file into your project `resources/views/` directory. You can query through Eloquent ORM and the DB facade as well.

Access the following url `http://localhost:8000/orm` in the browser to access the Eloquent query editor. The default passcode is `admin`

**FYI:** Currently, it is not recommended for PROD applications.

You can run Eloquent ORM queries as well as DB Facade queries in this panel:

```php
//run the queries via DB facade 
DB::table('employees')->join('job_history', 'employees.employee_id', '=', 'job_history.employee_id')->get() 

// to get the JSON response from the query
Employee::query()->with('job')->get()

// to get the query log along with the binding
Employee::query()->with('job')->toSql() 
```


