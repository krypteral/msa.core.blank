# msa.core.blank

## Before Launch

* Before starting, ensure that the `msa-core.crt` and `msa-core.key` files are present in the `docker/nginx/cert` folder.

If running in a local environment, these files can be omitted.

In this case, they will be automatically generated from `msa-core.local.crt` and `msa-core.local.key`,
and the project will be accessible at `msa-core.local`.

For this, you need to import the `localRootCA.pem` file into the list of trusted issuers:
```
Windows:

chrome://settings/security
-> Manage device certificates
  -> Trusted Root Certification Authorities -> Import
  -> Trusted Publishers -> Import

and restart the device
```

```
Ubuntu:

cd ./docker/nginx/cert
sudo cp localRootCA.pem "/usr/local/share/ca-certificates/localRootCA.crt"
sudo update-ca-certificates
```

```
MacOS:

cd ./docker/nginx/cert
sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain localRootCA.pem
sudo security add-trusted-cert -d -r trustAsRoot -k /Library/Keychains/System.keychain msa-core.local.crt
```

* Add to the `hosts` file:
```
Windows:

(open with administrator rights) C:\Windows\System32\drivers\etc\hosts
127.0.0.1 msa-core.local
```

```
Ubuntu:
MacOS:

/etc/hosts
127.0.0.1 msa-core.local
```

* Copy `.env.example` to `.env`
* Run `docker-compose up` in the root of the project
* Go to https://msa-core.local/

## Creating a new project from the template:

```
composer create-project krypteral/msa.core.blank:1.0.0 --repository-url=https://krypteral.github.io/msa.core.blank/ --no-interaction --remove-vcs <projectName>
cd <projectName>
```
