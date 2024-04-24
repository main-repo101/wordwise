### This is solely for educational purposes, not for widespread use. It's a project aimed at both building and learning the said System/Project 
## Composer
```
composer dump-autoload
```

## Database Config
#### By default:
- host = <b>'localhost'</b>
- username = <b>'root'</b>
- password = <b>''</b>
#### How to change it:
Go to the following files and add the red marked syntax and change the params' according to your needs;
<br/>
<br/>
<i>src/shs/project_wordwise/controller/<b>QuestAndAnswerHandler.php</b></i>
![QuestAndAnswerHandler.php](/.misc/res/img/quest-and-answer-handler.png)
<br/>
<br/>
<i>src/shs/project_wordwise/controller/<b>ParticipantHandler.php</b></i>
![QuestAndAnswerHandler.php](/.misc/res/img/participant-handler.png)

# Running/Executing 
1. Start/activate your "WEB SERVER" such as NGINX, APACHE or other web server. <br/>
2. Start/activate your "DATABASE" such as MariaDB, MongoDB, PostgreSQL, and other.<br/>
<i><b>NOTE:</b> Before activating the said database add a new database and name it <b>'word_wise'</b>.</i><br/>
3. php -S localhost:<b><your_preferred_port></b><br/>
<i>e.q:</i><br/>
```
php -S localhost:5501
```

# For CSS Styling Development
## Tailwindcss
```
npm install
```
```
npm run tailwindcss
```

# For Reseting
I will update/post it later... For now
please contact <i><b>main.repo101@gmail.com</b></i> 
#
#
### This is solely for educational purposes, not for widespread use. It's a project aimed at both building and learning the said System/Project 
