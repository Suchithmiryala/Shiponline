
ShipOnline - version 1.0 02/04/2018
Author - Phu Dao - 101335460

This system helps to manage online orders. It contains 6 php files.
1. shiponline.php: The home page that allows the user to navigate to other pages in the system.
2. register.php: This page allows new users to register into the system by entering their details and clicking 'register'. It checks 
the database to see if the user's email is already registered.
3. login.php: This page allows the user to log in to the system using their customer id and their password.
4. request.php: This page allows registered users to make orders/requests. Users enter in the specified information and click 'request'. It will return an error if data is invalid or if the user has not logged in.
5. admin.php: This page allows the administrator to view orders based on request date or pickup date.
6. settings.php: This file contains database settings for mysql queries.