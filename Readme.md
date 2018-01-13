![Technogram: A new social network for IT specialists](https://raw.githubusercontent.com/jookovjook/technogram-android/master/Art.png)

TechnoGram is a new social network for IT specialists. This repo provides Android part of the service.

- [Introduction](#introduction)
- [Features](#features)
- [Requirements](#requirements)
- [Communication](#communication)
- [Installation](#installation)
- [Overview](https://github.com/jookovjook/technogram-android#overview)
- [Credits](#credits)
- [Donations](#donations)
- [License](#license)

## Introduction

TechnoGram is a social network for IT specialists. There is a place, where it's users can:

• Share ther ideas and achievements to other people

• Discuss topics with

• Share their goals

Current repo represent server side of TechnoGram.

Android side available at [technogram-android](https://github.com/jookovjook/technogram-android) repo.

## Features

- [x] Username/e-mail authorization
- [x] Create posts with attached `images`, `description`, `@mentions`, `#hashtags`, `links`
- [x] See posts of other users
- [x] Leave comments to posts
- [x] Like, double-like on posts
- [x] Edit own profile (username, name, surname, email, bio)
- [x] See profiles of other users

## Requirements

- Android 5.0+ device
- LAMP server

## Communication

- If you **need help**, use [Stack Overflow](http://stackoverflow.com/questions/tagged/technogram). (Tag 'technogram')
- If you'd like to **ask a general question**, use [Stack Overflow](http://stackoverflow.com/questions/tagged/technogram).
- If you **found a bug**, open an issue.
- If you **have a feature request**, open an issue.
- If you **want to contribute**, submit a pull request.

## Installation

### LAMP

Firstly clone [technogram-server](https://github.com/jookovjook/technogram-server) to your LAMP server.

```bash
$ git clone https://github.com/jookovjook/technogram-server
```

Create an empty MySQL database and run `genearate.mysql` script.

Change `host`, `username`, `password`, `database` variables at `config.php`:

```PHP
define("DB_HOST", 'localhost');
define("DB_USER", 'username');
define("DB_PASSWORD", 'password');
define("DB_DATABASE", 'database');
```

Use the installation guide at [technogram-server](https://github.com/jookovjook/technogram-server) repository.

### Android Studio

Clone [technogram-android](https://github.com/jookovjook/technogram-android) respository 

```bash
$ git clone https://github.com/jookovjook/technogram-android
```

Follow instruction for [installation](https://github.com/jookovjook/technogram-android#android-studio) of Android app.

## Overview

[Overview](https://github.com/jookovjook/technogram-android#overview)

## Credits

Created by [jookovjook](https://github.com/jookovjook).

[vk.com/jookovjook](https://vk.com/jookovjook)

[fb.com/jookovjook](https://fb.com/jookovjook)

[t.me/jookovjook](https://t.me/jookovjook)
    
You are welcome to participate the project!

## Donations

I'll be gratefull if you donate some funds to my `Etherium wallet`:

```
0x9B9a7B954E4c634b200Be98aa602b7ee9006b05B
```

## License

TechnoGram is released under the Apache 2.0 license.

    Copyright 2017 JookovJook
    
    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at
    
        http://www.apache.org/licenses/LICENSE-2.0
    
    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.
