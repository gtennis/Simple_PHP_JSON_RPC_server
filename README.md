Simple_PHP_JSON_RPC_server
==========================

This is a simple lightweight JSON-RPC PHP web service server implementation, based on JSON-RPC web service specification (http://json-rpc.org/wiki/specification). It is not intended for production environments and should be used for demo purposes only.

The server simulates interaction between a simple eshop website. All the data is stored inside Sqlite database and consists of items, users, user_cart, orders and order_items tables.

Usage
--------------

Deploy the code on your prefered hosting server OR use live demo at "http://www.dotheapp.com/jsonrpc/" and perform request using POST HTTP method. Here are the request bodies for different tasks:

**Clear database**: {"method": "ClearDb", "params": {"api_key" : "my-app-secret-code"}, "id": null}

**Load 3 temporary items for testing**: {"method": "LoadItems", "params": {"api_key" : "my-app-secret-code"}, "id": null}

**Get item list**: {"method": "GetItems", "params": {"api_key" : "my-app-secret-code"}, "id": null}

**Create new user**: {"method": "CreateUser", "params": {"api_key" : "my-app-secret-code", "username" : "John", "pwd" : "123", "email" : "john@gmail.com"}, "id": null}

**Login with a user**: {"method": "LoginUser", "params": {"api_key" : "my-app-secret-code", "username" : "jonazzz", "pwd" : "123"}, "id": null}

Please note: use returned token as parameter for **Add items to cart**, **Get user cart items**, **Create order** and **Get user orders** requests.

**Add items to cart**: {"method": "AddToCart", "params": {"api_key" : "my-app-secret-code", "user_id" : 1, "item_id" : 1, "token" : "{ECD4E57E-C528-1AC1-BC23-4390629BFF5E}"}, "id": null}

**Get user cart items**: {"method": "GetCartItems", "params": {"api_key" : "my-app-secret-code", "token" : "{ECD4E57E-C528-1AC1-BC23-4390629BFF5E}"}, "id": null}

**Create order**: {"method": "CreateOrder", "params": {"api_key" : "my-app-secret-code", "user_id" : 1, "token" : "{ECD4E57E-C528-1AC1-BC23-4390629BFF5E}"}, "id": null}

**Get user orders**: {"method": "GetOrders", "params": {"api_key" : "my-app-secret-code", "token" : "{ECD4E57E-C528-1AC1-BC23-4390629BFF5E}"}, "id": null}


