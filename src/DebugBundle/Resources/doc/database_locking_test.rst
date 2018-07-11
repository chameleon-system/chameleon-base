Test Database Locking
=====================

To test the database locking in a running system by hand, the bundle offers three routes:

* /set/{value}
* /get
* /lock/{duration}


/set/{value}
------------

Sets the given value in the session without locking.
Redirects to /get when done

/get
----

Reads the current set value from the session

/lock/{duration}
----------------

Locks the session for the given period and writes "set from lock" as value


To test the active locking, you can write a value using the /set route.
You can then call the /lock route for eg. 5 seconds and - while it is running, try to set another value using the /set route.
You shouldn't be able to change the current value during that time and "set from lock" should be written in the session afterwards.

