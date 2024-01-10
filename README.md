Add the code from web.php to your project routes/web.php and copy the orm.blad.php file into your project resource/view/ directory.
You can query through Eloquent ORM and DB facade as well.

**FYI**: currently it is not recommended for the PROD application.

**You can run Eloquent ORM queries as well as DB Facade queries in this panel**

DB::table('employees')->join('job_history', 'employees.employee_id', '=', 'job_history.employee_id')->toSql()

Employee::query()->with('job')->get() **// to get the JSON response from the query**
Employee::query()->with('job')->toSql() **// to get the query log along with the binding**
