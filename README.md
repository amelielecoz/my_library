# My Library
An app to make available all the books I have in my personal library

#### Description:

This project aims to create a website to share all the books I have in my library.
My friends and family could connect and see the books I could lend them or share their own review about the book.

This website contains a front-end accessible to anybody and a back-end only accessible to authenticated admin.

It is written in PHP 8.1, built with Symfony 6.0 framework, Twig and Bootstrap.
The admin is built with EasyAdmin bundle. I used Docker and PostgresSQL to build up the DB.

- Public site : 
The front-end lists all the books, shows the comments about each book and allows any user to send a comment about a book.
The comment is not immediately published. I used Akismet API to detect spams. If Akismet consider the comment as spam, it is directly rejected.
If not an email is sent to the admin and ask for approval or rejection for the newly added comment. If approved the comment is then published on the website.
This process is asynchronous and uses Messages and Queues components of Symfony. Please see the 
[workflow](https://github.com/amelielecoz/my_library/blob/main/workflow.png) for a better understanding.


- Admin panel : To have access to the admin, the user must be authenticated. Then it has access to the dashboard that list all the books, authors, comments and gives the rights for creation, edition, deletion of any data.
This admin part also contains a form to add a book using [ISBN DB API](https://isbndb.com/). The user can enter a ISBN to look for a book and it is automatically added to the database (both book and authors).
If you want to try it out, please use ``` admin / admin``` after running the fixtures.


- Fixtures : During the development phase, I used fixtures to generate random data and fill in the DB. 
If you install the project, you can generate yours using this command :
```symfony console doctrine:fixtures:load --group=dev```


## How to start the project

### 1. Start servers
```symfony server:start```

### 2. Start docker
```docker-compose up -d```

### 3. Consume messages in the queue
```symfony console messenger:consume async -vv```

### 4. Watch for Sass changes
```symfony run -d yarn dev --watch```

### 5. Run tests
```make tests```


## Future developments :

- Users could send requests for the books they want to lend. Display the status (available/not available).
I would also add a page to display the requests according to their status (pending, approved) and to remind me who has which book.
- Push this website online.
- Implements CI/CD.
