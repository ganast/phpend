backstage-php is a very simple and limited-scope base for HTTP API backends in PHP. It provides out-of-the-box functionality for user account registration and password reset, authentication via username and password and endpoint access control as well as a simple mechanism for a simple mechanism for registration of custom endpoint handlers.

DISCLAIMER: backstage-php is nothing more than a playground for unrestricted experimentation and learning. No element of its design or implementation should be taken as suggested, preferred or meant to be used in the real world. On the contrary, there are most certainly better, established and well-decumented ways to design and implement each and every aspect of an API doing what backstage-php means to do (e.g., use a framework). You should not use backstage-php for any purpose other than to experiment.

DISCLAIMER: backstage-php does not offer any kind of ORM or other database abstraction logic. Custom database access is meant to be programmed directly in the data layer. You can import and use any tool you like to do that but backstage-php uses straight SQL to handle its own database needs and that is very much intended.

