<div id="top"></div>
<div align="right">

[![SymfonyInsight](https://insight.symfony.com/projects/30cae1fe-c708-4607-bbc9-c870c385cabe/small.svg)](https://insight.symfony.com/projects/30cae1fe-c708-4607-bbc9-c870c385cabe)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[![Codacy Badge](https://app.codacy.com/project/badge/Grade/70faed99b8b44df286c0b985a919e2a5)](https://www.codacy.com/gh/siakamansaly/Blog_PHP_MVC/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=siakamansaly/Blog_PHP_MVC&amp;utm_campaign=Badge_Grade)

</div>
<!-- PROJECT LOGO -->
<br />
<div align="center">
  <a href="https://github.com/siakamansaly/Blog_PHP_MVC">
    <img src="public/img/favicon/favicon.ico" alt="Logo">
  </a>
  <h1 align="center">Blog PHP MVC</h1>
  <p align="center">
    My first blog in PHP
  </p>
</div>

<!-- ABOUT THE PROJECT -->
## About The Project

<div align="center">
    <img src="public/img/Screenshot.png" alt="Screenshot" width="700px">
</div>
<p>The goal of this project is to create a blog in my colors to show my talents as PHP developer.
This project carried out in PHP, OOP and adopts an MVC pattern. It is also necessary to respect PHP Standard Recommandation and ensure that there are no security breaches. (Code quality monitoring on Codacy or SymfonyInsight)</p>
<p>The project contains:</p>
<ul>
  <li>a connection and registration system</li>
  <li>a frontend part with my presentation as well as all my articles</li>
  <li>a part allowing users to comment on an article</li>
  <li>a backend part to manage articles, comments and users.</li>
</ul>

<p align="right">(<a href="#top">back to top</a>)</p>

## Built With

This section should list any major frameworks/libraries used to bootstrap your project. Leave any add-ons/plugins for the acknowledgements section. Here are a few examples.
<ul>
  <li><a href="https://startbootstrap.com/" target="_blank">StartBootstrap</a></li>
  <li><a href="https://www.php.net/" target="_blank">PHP</a></li>
  <li><a href="https://jquery.com" target="_blank">JQuery</a></li>
  <li><a href="https://twig.symfony.com/" target="_blank">Twig</a></li>
</ul>

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- GETTING STARTED -->
## Getting Started

This is an example of how you may give instructions on setting up your project locally.
To get a local copy up and running follow these simple example steps.

1.Import database "BlogPerso.sql"

2.Clone the repo
   ```sh
   git clone https://github.com/siakamansaly/Blog_PHP_MVC.git
   ```
3.Install composer packages
   ```sh
   composer install
   ```
4.Rename **.env.example** to **.env** then you customize variables as needed to run the environment.
   ```sh
   DB_CONNECTION=mysql
   DB_HOST=localhost
   PORT=3306
   DB_NAME=database
   CHARSET=utf8
   DB_USER=root
   DB_PASSWORD=password
   HOST_SMTP=smtp.example.fr
   PORT_SMTP=587
   MAIL_FROM=example@example.com
   MAIL_REPLY=example@example.com
   MAIL_FIRSTNAME=Siaka
   MAIL_LASTNAME=MANSALY
   TITLE_WEBSITE="Blog de Siaka"
   META_AUTHOR="Siaka MANSALY"
   META_DESCRIPTION="Siaka MANSALY, Développeur PHP. Retrouvez mon profil, mon CV ainsi que mon blog."
   ```

5.Run project (Change **4000** by your local port)
   ```sh
   php -S localhost:4000 -t public/
   ```

6.For the first connection only:
   ```sh
   1.Create a personal account using the registration system.
   2.Connect with the initial admin account "admin@example.fr" - Password: "password"
   3.Then change the profile of your personal account to Administrator
   4.Delete account "admin@example.fr"  
   ```
<p align="right">(<a href="#top">back to top</a>)</p>

<!-- CONTRIBUTING -->
## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1.Fork the Project

2.Create your Feature Branch (`git checkout -b feature/AmazingFeature`)

3.Commit your Changes (`git commit -m 'Add some AmazingFeature'`)

4.Push to the Branch (`git push origin feature/AmazingFeature`)

5.Open a Pull Request

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- CONTACT -->
## Contact

Siaka MANSALY - [siaka.mansaly@gmail.com](siaka.mansaly@gmail.com) - [LinkedIn](https://www.linkedin.com/in/siaka-mansaly/)

Project Link: [https://github.com/siakamansaly/Blog_PHP_MVC](https://github.com/siakamansaly/Blog_PHP_MVC)
              
<p align="right">(<a href="#top">back to top</a>)</p>

## Acknowledgments

<ul>
  <li><a href="https://symfony.com/doc/current/components/http_foundation.html" target="_blank">HTTPFoundation</a></li>
  <li><a href="https://github.com/PHPMailer/PHPMailer" target="_blank">PHPMailer</a></li>
  <li><a href="https://github.com/cocur/slugify" target="_blank">Slugify</a></li>
  <li><a href="http://altorouter.com/" target="_blank">Altorouter</a></li>
  <li><a href="https://github.com/vlucas/phpdotenv" target="_blank">PHPdotenv</a></li>
  <li><a href="https://github.com/filp/whoops" target="_blank">Whoops</a></li>
</ul>

<p align="right">(<a href="#top">back to top</a>)</p>