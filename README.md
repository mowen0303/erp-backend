# erp-backend

Admin Account
- superadmin
- 111111
 
# problem solution
### godaddy 服务器发不出邮件
https://ca.godaddy.com/community/Managing-Email/PHP-mail-not-sending/td-p/2067
```
Solution 
 Re: PHP mail() not sending
This was marked as solved, but the posted solution didn't work for me. I was able to solve my issue, so I'll share what I did. If you use the Office365 email account purchased with your domain, and you want the php in your code to send emails to that address via a form, then you have to make sure the MX entry on the domain server matches the MX entry in on the hosting server. Here's what you do:

 

1. Login to your GoDadday account, and click on "My Products"

2. Find your target domain and click on the "DNS" button

3. Scroll down until you see the entry row for "MX" and find the "Value" column

4. Copy this part of the entry to your clipboard. (should be some kind of web address)

5. Open a new tab and login to cpanel (...yourdomain/cpanel)

6. Scroll to the bottom to find the "Email" section. Click "MX Entry"

7. Make sure "Remote Mail Exchanger" radio button is selected. If not, select it and click change. (*** This is important before changing the MX entry***)

8. At the bottom,under "MX Records", click edit (or add a new one if it doesn't already exist).

9. Replace the "Destination" by pasting what you copied from the GoDaddy DNS manager.

10. Click "Edit". Now you're done.

 

The "MX record" should now match whatever was in the "Value" column of the MX entry from the DNS manager, and your form should work. I would imagine the same thing would work for any remote email server (e.g. Google), as long as the MX entries match. I hope this helps the rest of you who still can't get the email form on your site to work. 

View solution in original post
```
 
 # godaddy refund
 ```
Sure(maisan6035@godaddy.com) and you can initiate chat and ask for chat transfer to me (Manish raj) i will be glad to assist you.
```

# cron
```
curl "http://example.com/?update_feedwordpress=1"
```

# Xdebug
https://www.youtube.com/watch?v=c9nQXHIb434
不要执行视频中`11:38`的命令，直接在`php.ini`的最后位置，把下面的配置粘贴进去就好，然后重新apach和phpstorm

```
zend_extension=/Applications/XAMPP/xamppfiles/lib/php/extensions/no-debug-non-zts-20170718/xdebug.so
xdebug.idekey=PHPSTORM
xdebug.mode=debug
xdebug.remote_enable=1
xdebug.remote_handler=dbgp
xdebug.remote_host=localhost
xdebug.remote_autostart = 1
xdebug.remote_port=9003
xdebug.show_local_vars=1
xdebug.remote_log=/Applications/XAMPP/logs/xdebug.log
```