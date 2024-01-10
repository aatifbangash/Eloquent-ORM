Add the code from `web.php` to your project routes (`routes/web.php`) and copy the `orm.blade.php` file into your project `resources/views/` directory. You can query through Eloquent ORM and the DB facade as well.

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
