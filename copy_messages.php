<?php
/**
 * This script allows to syncrhonice two imap mail accounts in different servers.
 * First, reads the messages existent in origin server inbox, and then copy them to target mailbox, deleting the messages in te original mailbox.
 * To execute this script  php-imap library has to be installed and active in the machine executing the script.
 *
 * @version 1.0.0 - 20171116
 * @author victorcorbacho AT iternova.net
 */

/**
 * Origin account configuration. Update with your origin mailbox server values.
 */
$origin[ 'server' ] = 'mail.foo.com';
$origin['port'] = 143;
$origin['username'] = 'mail@foo.com';
$origin['password'] = 'fantasticpassword';

/**
 * Destination account configuration. Update with your destination mailbox server values.
 */
$target[ 'server' ] = 'mail.bar.com';
$target['port'] = 993;
$target['username'] = 'mail@bar.com';
$target['password'] = 'incrediblepassword';

/**
 * IMPORTANT!!!!
 * Not change anything from here to the end
 */
$origin[ 'str_connection'] = '';
$target[ 'str_connection'] = '';

/**
 * Connection to mailboxes
 */
$array_mailbox = array( 'origin' => false, 'target' => false );

foreach( $array_mailbox as $dst => $mailbox ) {
	if( ${$dst}['port'] === 993 ) {
		// SSL connection
		${$dst}['str_connection'] = '/imap/ssl';
	} 
	$array_mailbox[ $dst ] = imap_open( '{' . ${$dst}['server'] . ':' . ${$dst}['port'] . ${$dst}['str_connection']  . '}INBOX', ${$dst}['username'], ${$dst}['password']); 

}

if ( in_array( false, $array_mailbox ) ) {
	throw new ImapException("Cannot connect to imap server.");
}

// Check number of messages to copy
$headers = imap_headers( $array_mailbox[ 'origin' ] );
$total = count( $headers );
$n = 1;

echo "$total messages ready to copy\n";

foreach( $headers as $key => $thisHeader ) {
	echo "....Message $n of $total";
	$header = imap_headerinfo($array_mailbox[ 'origin' ], $key+1);

            $is_unseen = $header->Unseen;

            $messageHeader = imap_fetchheader($array_mailbox['origin'], $key+1);
            $body = imap_body($array_mailbox['origin'], $key+1);
            if (imap_append($array_mailbox['target'], '{' . $target['server'] . ':' . $target['port'] . $target['str_connection'] . '}INBOX',$messageHeader."\r\n".$body)) {
                    if ($is_unseen != "U") {
                        if (! imap_setflag_full($array_mailbox['target'],$key+1,'\\SEEN')) {
                            echo "...couldn't set \\SEEN flag";
                        }
                    }
                echo "...done\n";
            } else {
                echo "...NOT done\n";
            }

            // Checking message as deleted
            imap_delete( $array_mailbox['origin'], $key+1 );
            $n++;
}


// Deleting messages
imap_expunge( $array_mailbox['origin'] );

// Closing mailboxes
imap_close($array_mailbox['origin']);
imap_close($array_mailbox['target']);
