Command line wrapper for `leafo\scssphp` complier

For documentation on that please go here:

https://github.com/leafo/scssphp

This also uses a few of my other projects as dependancies, primarally my CLI libarary:

https://github.com/ArtisticPhoenix/Cli



### Arguments ###
 LongName      | ShortName |   Required  | Required Value  | Notes
 ------------- | --------- | ----------- | --------------- | --------------------------------------
 help          |    h      |     no      |       no        | Displays the help document           
 bootstrap     |    b      |     yes     |       yes       |                                      
 debug         |    d      |     no      |       no        | Annotate selectors with CSS referring to the source file and line number         
 format        |    f      |     no      |       yes       | et the output format (compact, compressed, crunched, expanded, or nested)
 config        |    c      |     no      |       yes       | Config file, used to simplify the command line calls
 import        |    i      |     no      |       yes       | Set import path, multiple paths can be set with a comma seperated list                                                    
 linenumber    |    l      |     no      |       no        | Annotate selectors with comments referring to the source file and line number                                                   
 nocache       |    n      |     no      |       no        | Bypass caching, force compile                                                    
 mapsource     |    m      |     no      |       no        | Create source map file                                                    
 output        |    o      |     yes     |       yes       | File to output compiled css to (must end in .css)                                   
 precision     |    p      |     no      |       yes       | Set decimal number precision (default 10)         
 skip          |    s      |     no      |        no       | Continue compilation (as best as possible) when error encountered                  
 cache         |    a      |     no      |       yes       | Cache file path, filenames are ignored (defult the current dir)
 tree          |    t      |     no      |       no        | Dump formatted parse tree
 version       |    v      |     no      |       no        | Print the version 
 
 **Terms**
 
 - __LongName__ when using in the CLI long names should begin with `--` (double hyphen) for example `php /pathto/SCSS --help`.  For HTTP requests, simply use either the longname or the shortname as part of the request data. for example `www.localhost/SCSS?help`.
 - __ShortName__ when using in the CLI shourt names should begin with `-` (single hypen) for example `php /pathto/SCSS -h`  For HTTP requests, simply use either the longname or the shortname as part of the request data. for example `www.localhost/SCSS?h`
 - __Required__ These arguments must be present in either the `--config` file or as part of the request.
 - __Required Value__ If these arguments are present they must have a value.  They are not nessacarly required for the application to run, but when included they must contain a value.
 
 
One of the best examples I can show is using the help argument.

```
#via HTTP get (short name)
http://localhost/SCSS/?h
http://localhost/SCSS/?help

#via Command line
php /home/app/SCSS/index.php -h
php /home/app/SCSS/index.php --help
```

In either case your output should be something like this:

```
Usage: php  [--] [args...]
    -h, --help           Show this help document
    -b, --bootstrap      SCSS Bootstraper file (should include @imports)
    -d, --debug          Annotate selectors with CSS referring to the source file and line number
    -f, --format         Set the output format (compact, compressed, crunched, expanded, or nested)
    -c, --config         Config file
    -i, --import         Set import path, multiple paths can be set with a comma seperated list
    -l, --linenumber     Annotate selectors with comments referring to the source file and line number
    -n, --nocache        Bypass caching
    -m, --mapsource      Create source map file
    -o, --output         File to output to
    -p, --precision      Set decimal number precision (default 10)
    -s, --skip           Continue compilation (as best as possible) when error encountered
    -a, --cache          Cache file path, filenames are ignored (defult the current dir)
    -t, --tree           Dump formatted parse tree
    -v, --version        Print the version

```
                              
**Installation**

You can get it from composer, by requiring it.

```
"require" : {
    "evo/cli" : "~1.0"
}
```

It has some dependancies (which are included in the composer.json file).

```
"require" : {
	"php" : ">=5.6",
	"evo/patterns" : "~1.0",
	"evo/exception" : "dev-master",
	"leafo/scssphp" : "~0.7",
	"evo/cli" : "~1.0"
}
```

While not really being meant for use through the browser you can use it that way if you really want to.  In generally you would not include this libarary with your project.  Instead install it someplace outside of you project.  Then add a config file like this somewhere in your project:

```
if(!defined('ARTISTICPHOENIX_DIR')) define('ARTISTICPHOENIX_DIR', str_replace('\\','/', realpath(__DIR__.'/../..')));
return array(
    'output'        => ARTISTICPHOENIX_DIR.'/css/style-override.css',
    'import'        => ARTISTICPHOENIX_DIR.'/scss',
    'cache'         => __DIR__,
    'format'        => 'expanded',//compact|compressed|crunched|expanded|nested
    'bootstrap'     => 'bootstrap', 
);
```

All of these setting can also be sent directly via arguments thorugh the CLI, GET or even POST.




