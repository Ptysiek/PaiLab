<?php

namespace Nieruchomosci\Model;

use Laminas\Mail\Message;
use Laminas\Mail\Transport\Smtp as SmtpTransport;
use Laminas\Mail\Transport\SmtpOptions;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Part as MimePart;
use Laminas\Mime\Mime;
use Laminas\Db\Adapter as DbAdapter;
use Laminas\Db\Sql\Sql;


class Zapytanie implements DbAdapter\AdapterAwareInterface
{
    use DbAdapter\AdapterAwareTrait;
    private array $smtpTransportConfig;
    private array $from;

    public function __construct(array $config)
    {
        $this->from = $config['from'];
        unset($config['from']);

        $this->smtpTransportConfig = $config;
    }

    /**
     * Wysya maila z zapytaniem ofertowym.
     *
     * @param array  $daneOferty
     * @param string $tresc
     * @return bool
     */
    public function wyslij($daneOferty, $tresc, $email, $telefon)
    {
        $transport = new SmtpTransport();
        $options = new SmtpOptions($this->smtpTransportConfig);
        $transport->setOptions($options);

        $part = new MimePart("Klient wyraził zainteresowanie ofertą numer *$daneOferty[numer]* o treści:\n\n$tresc\n\nemail: $email\ntelefon: $telefon");
        $part->type = 'text/plain';
        $part->charset = 'utf-8';

        $body = new MimeMessage();
        $body->setParts([$part]);

        $message = new Message();
        $message->setEncoding('UTF-8');
        $message->setFrom($this->from['email'], $this->from['name']); // konto do wysyłania maili z serwisu
        $message->addTo('', "Odbiorca"); // osoba obsługująca zgłoszenia
        $message->setSubject("Zainteresowanie ofertą");
        $message->setBody($body);

        try {
            $transport->send($message);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    public function dodaj($id_oferty, $tresc, $email, $telefon)
    { 
        $dbAdapter = $this->adapter;		
		$sql = new Sql($dbAdapter);
		$insert = $sql->insert('zapytania');
		$insert->values([
			'id_oferty' => $id_oferty,
            'tresc' => $tresc,
            'email' => $email,
            'telefon' => $telefon
        ]);
        $selectString = $sql->buildSqlString($insert);
		$wynik = $dbAdapter->query($selectString, $dbAdapter::QUERY_MODE_EXECUTE);		
        try {
			return $wynik->getGeneratedValue();
		} catch(\Exception $e) {
			return false;
		}
    }

    public function wyslijPdf($daneOferty, $pdfFile, $email)
    {
        $transport = new SmtpTransport();
        $options = new SmtpOptions($this->smtpTransportConfig);
        $transport->setOptions($options);

        $part = new MimePart("Szczegóły oferty w załączeniu");
        $part->type = 'text/plain';
        $part->charset = 'utf-8';

        $pdf = new MimePart($pdfFile);
        $pdf->type = 'application/pdf';
        $pdf->filename = 'oferta.pdf';
        $pdf->disposition = Mime::DISPOSITION_ATTACHMENT;
        $pdf->encoding = Mime::ENCODING_BASE64;

        $body = new MimeMessage();
        $body->setParts([$part, $pdf]);

        $message = new Message();
        $message->setEncoding('UTF-8');
        $message->setFrom($this->from['email'], $this->from['name']); // konto do wysyłania maili z serwisu
        $message->addTo($email, "Odbiorca"); // osoba obsługująca zgłoszenia
        $message->setSubject("Oferta numer *$daneOferty[numer]*");
        $message->setBody($body);
        try {
            $transport->send($message);
            return true;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }
}
