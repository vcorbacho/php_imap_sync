# What is php_imap_sync
Is a cript to synchronice existing imap accounts in two servers, using imap library from php.

I use it to synchronice mails from old server where host provider don't allow redirection, to my new email server, so I don't loss any mail from old mailbox.

You can configure execution in crontab to automtice synchronization.

The script will take all the messages at inbox folder in origin server mailbox, copy them to inbox folder at destination server, and delete them from origin server.

# Installation and execution
Just clone project in your computer or server and execute with 
<code bash>
  php copy_messages.php
</code>
