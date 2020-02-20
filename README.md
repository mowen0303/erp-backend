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
