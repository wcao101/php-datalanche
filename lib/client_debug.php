<?php
/* DEPENDICES */
global runtime_errors = array();
global runtime_warnings = array();
class runtime_error
{
    private $line;
    private $function;
    private $content = array();
    private $data_dump;

    public function __construct($_line = NULL, $_function = NULL, $_content = NULL, $_data_dump = False)
    {
        if ($_line != NULL)
        {
            $this->line = $_line;
        }
        if ($_function != NULL)
        {
            $this->function = $_function;
        }
        if ($_content != NULL)
        {
            if (is_array($_content))
            {
                foreach ($c as $_content) 
                {
                    $this->content->append($c);
                }
            }else
                {
                    $this->content->append($_content);
                }
        }
        if ($data_dump != NULL)
        {
            if (is_bool($data_dump))
            {
                $this->data_dump = $_data_dump;
            }else
                {
                    $this->content->append('AN INVALID BOOLEAN WAS SUPPLIED TO data_dump');
                }
        }
    }
}

class runtime_info
{
    private $line;
    private $function;
    private $content = array();
    private $data_dump;

    public function __construct($_line = NULL, $_function = NULL, $_content = NULL, $_data_dump = FALSE)
    {
        if ($_line != NULL)
        {
            $this->line = $_line;
        }
        if ($_function != NULL)
        {
            $this->function = $_line;
        }
        if ($_content != NULL)
        {
            if (is_array($_content))
            {
                foreach ($_content as $_value) 
                {
                    $this->content->append($_value)
                }
            }
            $this->content = $_content;
        }
        if ($data_dump != NULL)
        {
            if (is_bool(data_dump))
            {
                this->data_dump = $_data_dump;
            }
        }
    }
}

class runtime_printer
{
    public function __construct($object)
    {
        try
        {
            foreach ($object as $i => $value) 
            {
                echo ":::".$i." printing..\n";
                echo "->:(".$i.")"."LINE #: ".$value->line."\n";
                echo "->:(".$i.")"."FUNCTION: ".$value->function."\n";
                
                foreach (($value->content) as $_i => $_content) 
                {
                    echo "-->:(".$_i.") error: ".$_content;
                }
                
                echo "->:(".$i.")"."CONTENT: ".$value->content."\n";
                //Dealing with wherther we dump the variable or not
                if ($value->data_dump)
                {
                    echo "!!->:(".$i.")"."WARNING DUMPING DATA..\n";
                    var_dump($value)
                    echo "\n-------------------------------\n";
                    echo "END DUMP FROM: (".$i.") CONTINUING..\n";
                }
            }
        }
    }
}
?>