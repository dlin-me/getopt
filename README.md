# Dlin Geocoder PHP Lib


## 1. Overview

This is a PHP Geocoding lib using Google Map API and/or Microsoft Bing Map API.

The goal is to make geocoding easy with Google Map API and/or Bing Map API. The library provides two methods, *forward* and *reverse*, for **Forward Geocoding** and **Revesre Geocoding**

>Forward Geocoding is the process of taking a given location in address format and returning the closes known coordinates to the address provided. The address can be a country, county, city state, zip code, street address, or any combination of these.



>Reverse Geocoding is the opposite of Forward Geocoding.
It takes the provided coordinates (latitude and longitude)
and provides you the closest known location to that point in address format.


This lib accepts multiple Google accounts and/or Bing accounts configuration to avoid problems caused by excessive API usage. If called multiple times, accounts are rotated and used in turns. Accounts also back each other up. If one account fails, other accounts will be used as backup to complete the task.




## 2. Installation

With composer, add to your composer.json :

```
{
    require: {
        "dlin/getopt": "dev-master"
    }
}
```


## 3. Usage
This library class, hereafter refered to as **Dlin Geocoder**, comes with a very simple interface with only two methods. One for **Forward Geocoding** and one for **Revesre Geocoding**

#### Constructor
The constructor of the Geocoder class takes an array of configuration as the parameter. Please refer to the configuration section for details.

```
$config = array();
$config['my google']['vendor'] = 'google';
$config['my google']['weight'] = 2; //greater the weight, higher the priority
$config['my bing']['vendor'] = 'bing';
$config['my bing']['key'] = 'Your_Bing_account_key';
$config['my bing']['weight'] = 1;

$coder = new Geocoder(config);
```




#### Forward Geocoding

```
$address = $coder->forward("1 Queen Street, Melbourne, Vic, AU");
```


#### Reverse Geocoding

```
$address = $coder->reverse("-33.86687", "151.19565");

```

#### The Returned Value ( Address Object)
The returned value of the above two method is a Dlin/Geocoder/Address DTO object

```

$address = $coder->reverse("-33.86687", "151.19565");
print_r($address);

/* ========= out put ============

Dlin\Geocoder\GeoAddress Object
(
    [addressLine1] => 48 Pirrama Rd
    [addressLine2] =>
    [suburb] => Pyrmont
    [state] => NSW
    [postcode] => 2009
    [country] => Australia
    [latitude] => -33.86687
    [longitude] => 151.19565
    [partial] =>
    [formattedAddress] => 5/48 Pirrama Road, Pyrmont NSW 2009, Australia
    [geoCoding] => my bing
)

========= end out put ============ */

```


Most of the fields are self-explanatory, the *partial* field indicates if the finding is an approximate (alway false for *reverse* method ), the *geoCoding* field shows the Google/Bing account used.


## 4. Configuration

The *Dlin Geocoder* lib constructor takes either an array of account configuration, or a file path to a *.ini* configuration file.

#### Configuration Array

The configuration array past to the consturctor must be in the form of :

```
$config['account nick name'] = array('vendor'=>'[google or bing]', 'weight'=>[weight], 'key'=>'account key', 'client'=>'google client id' );


```

* You can give each a account a nick name and if that account is used in geocoding, it will be set to the *geoCoding* field of the returned Address object;

* The 'vendor' field is either 'google' or 'bing', currently these are the only two vendors supported

* The 'weight' field sets the priority of the account, account with higher priority will be used first, however, subsequent calls in the same script will use other accounts in turn if more than one accounts are available.

* The 'key' field is either the Key for the Bing account or the private/secrete key for a Google account

* The 'client' field is Client Id of a Google account

#### Configuration file

The lib can also accepts an .ini file as the configuration. Fields in the configuration files mirrors the definition of a configuration array covered above. Here is an example:

```
; This is a sample configuration file
; Comments start with ';'



;[Provider 1]
;vendor = google
;client = your_google_client_id
;key = your_private_key
;weight = 1;

[Provider2]
vendor = google
weight = 19

[Provider3]
vendor = bing
key = AvwNOuwxCZESwB9_p_RAHncR-oypS6UTsc5_g9u4Ejyt32G59_kKnvTSG3ySE3Q8
weight = 14

[Provider4]
vendor = bing
key = ApUmGCPD3VPcMRjlZUjVz1ZruPHhlZYqA6Up9wvOVjQrYmJlygS3ftM87SHlIyx9
weight = 5



```











## 5. License


This library is free. Please refer to the license file in the root directory for detail license info.

