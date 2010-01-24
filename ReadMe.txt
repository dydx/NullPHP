/------------------------------------------------------------------------/
/                                                                        /
/                      NullPHP Readme documentation                      /
/                                                                        /
/                NULL Byte Design <josh@thenullbyte.org>                 /
/                                                                        /
/------------------------------------------------------------------------/

Thank you for downloading NullPHP. NullPHP provides a lightweight and easy
to use solution for object oriented project design. By providing a base to
work off of which includes basic Model-View-Controller style relations, as
well as a simplistic approach to Object-Relational-Mapping, NullPHP is a
a strong, reliable, and extendable platform on which to build applications.

There are two distinct parts of NullPHP which can be used seemlessly
together, or by themselves. First is the Model-View-Controller (MVC) side,
which borrows from the BareBonesMVC library. This single paged script
allows for a simple and efficient way to separate programming logic from
user centered styling. The next portion is an in-house solution to the
idea of Object-Relational-Mapping. Treating tables of databases as objects
allows for easier to manage scripts as well as more control over data flow.

There are also smaller libraries associated with NullPHP, which you may or
may not decide to use or need. The first of which is the Error Handler
library. In more advanced applications, it is wise to use exceptions rather
than core-generated errors. Exceptions give you greater control over how
errors are displayed, and how they affect the running of your application.

To utilize add a 'require_once()' statement in your index.php file, and
everything is taken care of. Exceptions triggered in the application will
appear in a more friendly and helpful manner, which will provide more
information on what exactly went wrong, where it went wrong, and why.


