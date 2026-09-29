\# 🌐 Personal Portfolio \& Travel Community Website



A dynamic and responsive personal portfolio website developed as a Web Development academic project. The website combines a personal portfolio, travel gallery, community posting system, user authentication, messaging, and an administrative management panel.



The project is built using \*\*PHP, MySQL, HTML5, CSS3, and JavaScript\*\*, with \*\*XAMPP\*\* used as the local development environment.



\---



\## 📌 Project Overview



This project was developed to demonstrate practical web development skills, including frontend design, backend programming, database management, authentication, CRUD operations, file handling, and interactive web features.



The website provides visitors with information about the developer while also allowing registered users to interact with travel-related content through posts, comments, likes, and image sharing.



\---



\## ✨ Key Features



\### 👤 Personal Portfolio



\* Personal introduction and biography

\* Skills and profile information

\* Hero section

\* Contact information

\* Responsive website layout



\### 🧳 Travel Section



\* Travel destination showcase

\* Travel stories and posts

\* Travel images

\* Destination-based content



\### 📸 Photo Gallery



\* Personal photo albums

\* Image gallery

\* Photo comments

\* Like functionality



\### 👥 Community System



\* User registration and login

\* Community travel posts

\* Post approval system

\* Comments and replies

\* Like functionality

\* User-generated content



\### 💬 Contact \& Messaging



\* Contact form

\* Message submission

\* Admin message management

\* Message reading and reply functionality



\### 🔐 Authentication \& Administration



\* User authentication

\* Admin login

\* Admin dashboard

\* User management

\* Comment management

\* Travel post management

\* Website settings management

\* Hero section management

\* About section management

\* Analytics dashboard



\### 📱 Responsive Design



\* Mobile-friendly layout

\* Responsive navigation

\* Modern visual design

\* Image-based travel presentation



\---



\## 🛠️ Technologies Used



| Technology     | Purpose                       |

| -------------- | ----------------------------- |

| \*\*HTML5\*\*      | Website structure             |

| \*\*CSS3\*\*       | Styling and responsive design |

| \*\*JavaScript\*\* | Client-side interactivity     |

| \*\*PHP\*\*        | Backend development           |

| \*\*MySQL\*\*      | Database management           |

| \*\*XAMPP\*\*      | Local development environment |

| \*\*Apache\*\*     | Local web server              |



\---



\## 📂 Project Structure



```text

portfolio\_project/

│

├── admin/

│   ├── admin.php

│   ├── admin\_analytics.php

│   ├── admin\_users.php

│   ├── admin\_comments.php

│   ├── admin\_messages.php

│   ├── admin\_travels.php

│   └── ...

│

├── css/

│   └── style.css

│

├── images/

│   ├── hero.jpg

│   ├── travel-bg.jpg

│   ├── beijing.jpg

│   ├── hangzhou.jpg

│   ├── nanjing.jpg

│   ├── paris.jpg

│   ├── shanghai.jpg

│   ├── tokyo.jpg

│   └── ...

│

├── js/

│   └── .js

│

├── php/

│   └── contact\_process.php

│

├── about.php

├── album.php

├── community\_posts.php

├── index.php

├── login.php

├── logout.php

├── my\_gallery.php

├── register.php

├── travels.php

├── upload\_post.php

├── save\_comment.php

├── send\_message.php

├── portfolio\_db.sql

└── README.md

```



\---



\## 🚀 Installation \& Setup



\### 1. Install XAMPP



Install \*\*XAMPP\*\* and start:



\* Apache

\* MySQL



\### 2. Copy the Project



Place the project folder inside the XAMPP `htdocs` directory:



```text

C:\\xampp\\htdocs\\portfolio\_project

```



\### 3. Create the Database



Open:



```text

http://localhost/phpmyadmin

```



Create a new MySQL database for the project.



Then import the database SQL file into the newly created database.



> The database dump included in the original development environment may contain development/sample data. For a public deployment, use sanitized data and update the database configuration with your own local credentials.



\### 4. Configure Database Connection



Update the database connection settings in the PHP configuration used by the project.



Typical local XAMPP configuration:



```text

Host: localhost

Username: root

Password: 

Database: your\_database\_name

```



\### 5. Run the Website



Open your browser and visit:



```text

http://localhost/portfolio\_project/

```



\---



\## 🖥️ Main Pages



| Page                  | Description                  |

| --------------------- | ---------------------------- |

| `index.php`           | Homepage                     |

| `about.php`           | About / personal information |

| `travels.php`         | Travel section               |

| `album.php`           | Photo gallery                |

| `community\_posts.php` | Community posts              |

| `my\_gallery.php`      | User gallery                 |

| `login.php`           | User login                   |

| `register.php`        | User registration            |

| `admin/`              | Administrative panel         |



\---



\## 📸 Screenshots



\### Homepage



Add your homepage screenshot here:



```markdown

!\[Homepage Screenshot](images/homepage.png)

```



\### Travel Section



```markdown

!\[Travel Section](images/travel-section.png)

```



\### Admin Dashboard



```markdown

!\[Admin Dashboard](images/admin-dashboard.png)

```



> Replace the example screenshot filenames with the actual screenshots you want to showcase.



\---



\## 🎓 Academic Information



\*\*Project Type:\*\* Web Development Course Project



\*\*University:\*\* Nanjing Tech University



\*\*Department:\*\* Computer Science and Technology



\*\*Project:\*\* Personal Portfolio \& Travel Community Website



\*\*Developer:\*\* Kawsarul Islam



\*\*Academic Year:\*\* 2026



\---



\## 🎯 Learning Objectives



Through this project, I developed practical experience in:



\* Designing responsive web interfaces

\* Developing dynamic websites using PHP

\* Working with MySQL databases

\* Implementing CRUD operations

\* Creating authentication systems

\* Handling forms and user input

\* Managing uploaded content

\* Building administrator dashboards

\* Connecting frontend and backend components

\* Organizing a complete web development project

\* Using Git and GitHub for version control



\---



\## 🔒 Security Considerations



For public deployment, the following should be reviewed before production use:



\* Database credentials should never be committed to a public repository.

\* Real user data should be removed from development database dumps.

\* Passwords should be securely hashed.

\* Uploaded files should be validated and restricted.

\* Production configuration should use environment variables.

\* User input should be



