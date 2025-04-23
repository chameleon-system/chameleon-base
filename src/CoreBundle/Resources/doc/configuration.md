# Configuration

## E-Mail Peer Security

PHP can check the SSL/TLS certificate of a target SMTP server when sending mails. In PHP 5.6+ this check is enabled by
default, in previous versions it is disabled. Chameleon unifies this behaviour: Peer checks are always disabled by default,
but can be enabled by configuration.

```yaml
chameleon_system_core:
  mailer:
    peer_security: strict|permissive
```

Only strict and permissive are allowed, with permissive being the default.
In strict mode, Chameleon will set the values verify_peer = true, verify_peer_name = true (PHP 5.6+), allow_self_signed = false.
In permissive mode the values are set to false/false/true instead.

Note that to enable strict mode not only the SMTP server needs to be configured correctly, but also the project PHP server
needs to be able to recognize the certificate authority (which might not yet be the case with Let's Encrypt). Be sure to
test the strict setting before applying it to a production environment.

In the future the default value is likely to be changed to strict.

## Handling E-Mails in Dev or Staging Environments

We usually want to sent all mails on a dev system to the developer working on it, all mails on a staging server
should reach their target if that happens to be the customer or the developers working on it, while all mails on live
should go to their intended targets. To solve this, chameleon allows you to enable a mail target transformation service.

The service allows you to redirect all mails that do not match a white list to a user defined address.

The white list is defined as follows: mail;mail;@white-listed-domain.tld;@PORTAL-DOMAINS

Domains will match both with and without "www." prefix. @PORTAL-DOMAINS will white list all domains of the active portal.

you may also define a prefix for your mails. Please note that the prefix will be added even if you disable the transformation service.

```yaml
chameleon_system_core:
  mail_target_transformation_service:
    target_mail: "send-all-mail-here@example.com"
    enabled: true
    subject_prefix: "prefix mail subjects with"
    white_list: "@esono.de"
```

## Whitelisting Access to Cronjob Trigger

To use a whitelist of IPs to limit access to the cronjob trigger endpoint (runcrons), specify the list in your config:

```yaml
chameleon_system_core:
  cronjobs:
    ip_whitelist: ["1.1.1.1", "2.2.2.2"]
```

The configuration does not work with ip ranges, all IPs must be whitelisted separately.

An empty list means, that there is no limitation. This is the default setting, so to avoid limiting access, just don't specify anything.