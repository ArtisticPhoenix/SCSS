<?php
namespace evo\scss;

use evo\pattern\singleton\SingletonInterface;
use evo\pattern\singleton\SingletonTrait;
use evo\cli\Cli;
use Leafo\ScssPhp\Compiler;
use evo\exception\InvalidArgument;
use Leafo\ScssPhp\Version;

class Scss implements SingletonInterface{
    use SingletonTrait;
    
    const VERSON = '1.0.0';
    
    const CACHE_FILENAME = 'scssCache.php';
    
    /**
     * 
     * @var Cli
     */
    protected $Cli;

    
    protected function init(){
        $config = [
            [
                'shortName' => 'h',
                'longName'  => 'help',
                'doc'       => 'Show this help document'
            ],[
                'shortName' => 'b',
                'longName'  => 'bootstrap',
                'doc'       => 'SCSS Bootstraper file (should include @imports)',
                'options'   => ['requireValue' => true]
            ],[
                'shortName' => 'd',
                'longName' => 'debug',
                'doc' => 'Annotate selectors with CSS referring to the source file and line number'
            ],[
                'shortName' => 'f',
                'longName' => 'format',
                'doc' => 'Set the output format (compact, compressed, crunched, expanded, or nested)',
                'options'   => [
                    'accept' => function($arg, $value){
                        return preg_match('/^(compact|compressed|crunched|expanded|nested)$/i', $value);
                    },
                    'requireValue' => true
                ]
            ],[
                'shortName' => 'c',
                'longName' => 'config',
                'doc' => 'Config file',
                'options'   => [
                    'accept' => function($arg, $value){
                        return is_file($value);
                    },
                    'requireValue' => true
                ]
            ],[
                'shortName' => 'i',
                'longName'  => 'import',
                'doc'       => 'Set import path, multiple paths can be set with a comma seperated list',
                'options'   => [
                    'requireValue' => true
                ]
            ],[
                'shortName' => 'l',
                'longName'  => 'linenumber',
                'doc'       => 'Annotate selectors with comments referring to the source file and line number'
            ],[
                'shortName' => 'n',
                'longName'  => 'nocache',
                'doc'       => 'Bypass caching'
            ],[
                'shortName' => 'm',
                'longName'  => 'mapsource',
                'doc'       => 'Create source map file'
            ],[
                'shortName' => 'o',
                'longName'  => 'output',
                'doc'       => 'File to output to',
                'options'   => [
                    'accept' => function($arg, $value){
                        if(".css" != strrchr($value,".")) throw new InvalidArgument("Invalid file extension $value");
                    
                        if(!is_dir(dirname($value)))  throw new InvalidArgument("Invalid path ".htmlspecialchars(dirname($value)));
                    
                        if(!is_file($value) && !file_put_contents($value, "\n")){
                            throw new InvalidArgument(htmlspecialchars($value)." is not a file");
                        }
                        return true;
                    },
                    'requireValue' => true
                ]
            ],[
                'shortName' => 'p',
                'longName'  => 'precision',
                'doc'       => 'Set decimal number precision (default 10)',
                'options'   => [
                    'accept' => function($arg, $value){
                        return preg_match('/^\d+$/', $value);
                    },
                    'requireValue' => true
                ]
            ],[
                'shortName' => 's',
                'longName'  => 'skip',
                'doc'       => 'Continue compilation (as best as possible) when error encountered'
            ],[
                'shortName' => 'a',
                'longName'  => 'cache',
                'doc'       => 'Cache file path, filenames are ignored (defult the current dir)',
                'options'   => [
                    'accept' => function($arg, $value){
                        if(!is_dir(dirname($value))) throw new InvalidArgument("Invalid path ".htmlspecialchars(dirname($value)));

                        return true;
                    },
                    'requireValue' => true
                ]
            ],[
                'shortName' => 't',
                'longName'  => 'tree',
                'doc'       => 'Dump formatted parse tree'
            ],[
                'shortName' => 'v',
                'longName'  => 'version',
                'doc'       => 'Print the version'
            ]
        ];
          
        $this->Cli = Cli::getInstance();
        $this->Cli->fromConfig($config);
        $this->Cli->setAllowedRequestTypes(Cli::R_ALL);
        
        /*if(Cli::REQUEST_CLI == $this->Cli->getCurrentRequestType()){
            header('Content-type: text/plain');
        }elseif(Cli::REQUEST_POST | Cli::REQUEST_GET & $this->Cli->getCurrentRequestType()){
            header('Content-type: text/html');*/
            echo "<pre>";
       /* }*/

    }
    
    
    public function run() {

        if($this->Cli->getArguments('h')) $this->Cli->printHelpDoc();
        if($this->Cli->getArguments('v')) exit(self::VERSION . "\n");
        if($this->Cli->getArguments('c')){
            $config = require $this->Cli->getArguments('c');
            print_r($config);
            
            $this->Cli->setRequest($config);
        }
        
        $Pscss = new Compiler();
        
        if($this->Cli->getArguments('d')) $Pscss->setLineNumberStyle(Compiler::DEBUG_INFO);
        
        if($this->Cli->getArguments('l')) $Pscss->setLineNumberStyle(Compiler::LINE_COMMENTS);
        
        if($this->Cli->getArguments('s')) $Pscss->setIgnoreErrors(true);
        
        if($this->Cli->getArguments('i')) $Pscss->setImportPaths(array_filter(array_map('trim', explode(',', $this->Cli->getArguments('i')))));
        
        if($this->Cli->getArguments('p')) $Pscss->setNumberPrecision($this->Cli->getArguments('p'));
        
        if($this->Cli->getArguments('f')) $Pscss->setFormatter('\\Leafo\\ScssPhp\\Formatter\\' . ucfirst(strtolower($this->Cli->getArguments('f'))));
        
        if($this->Cli->getArguments('m')) $Pscss->setSourceMap(Compiler::SOURCE_MAP_FILE);
        
        $needs_compile = false;
        if(!$this->Cli->getArguments('n')){
            $scss_cache_file = rtrim($this->Cli->getArguments('a', __DIR__). '\/').'/'.self::CACHE_FILENAME;
            if(!is_file($scss_cache_file)) file_put_contents($scss_cache_file, "");
            
            $scss_cache = require $scss_cache_file;
            $scss_cache = is_array($scss_cache) ? $scss_cache : [];
            
            $scss_files = array_fill_keys(glob($this->Cli->getArguments('i').'/*.scss'), '');
            foreach ($scss_files as $file => &$h){
                $hash = hash_file('sha1', $file);
                if(!isset($scss_cache[$file]) || $scss_cache[$file] != $hash) $needs_compile = true;
                $h = $hash;
            }
            $scss_cache = array_replace($scss_cache, $scss_files);
            
            if($needs_compile) file_put_contents($scss_cache_file, '<?php return '.var_export($scss_cache,true).';');
        }
        
        $scss = '';
        
        if($needs_compile){
            if($this->Cli->getArguments('b')){
                $scss = $Pscss->compile('@import "'.$this->Cli->getArguments('b').'";');
            }else{
                throw new InvalidArgument('Bootstrap [-b, -bootstrap] is required');
            }
            
            if($this->Cli->getArguments('o')){
                file_put_contents($this->Cli->getArguments('o'), $scss);
            }else{
                echo $scss;
            }
        }else{
            echo file_get_contents($this->Cli->getArguments('o'));
        }
        
    }  
}
