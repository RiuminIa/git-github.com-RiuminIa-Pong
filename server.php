<?php
// ini_set('display_errors', 1);
//     ini_set('display_startup_errors', 1);
//     error_reporting(E_ALL);
//php server2.php start
    use Workerman\Worker;
    use Workerman\Lib\Timer;
    require_once __DIR__.'/vendor/autoload.php';
    $context = [
        'ssl' => [
            'local_cert'  => '/home/xriumin/webte_fei_stuba_sk.pem',
            'local_pk'    => '/home/xriumin/webte.fei.stuba.sk.key',
            'verify_peer' => false,
        ]
    ];
    $ws_worker = new Worker("websocket://0.0.0.0:9000", $context);
    $ws_worker->transport = 'ssl';
 
    // 4 processes kolko hracov
    $speed=8;
    $angel=rand(0,360);
    $speedY=$speed*sin(deg2rad($angel));
    $speedX=$speed*cos(deg2rad($angel));;
    $x=48.5;
    $y=48.5;
    $timer=0.1;
    $players=[4];
    $players[0] = [ 'addres'=>null, 'name'=>'player1', 'id'=>'playerLeft', 'divId'=>'blackHollLeft','top'=>false, 'down'=>false, 'hels' =>3,'index'=>0, 'first'=>false, 'status'=>null];
    $players[1] = [ 'addres'=>null, 'name'=>'player2', 'id'=>'playerRight', 'divId'=>'blackHollRight','top'=>false, 'down'=>false,  'hels' =>3,'index'=>1, 'first'=>false, 'status'=>null];
    $players[2] = [ 'addres'=>null, 'name'=>'player3', 'id'=>'playerTop', 'divId'=>'blackHollTop','top'=>false, 'down'=>false,  'hels' =>3,'index'=>2, 'first'=>false, 'status'=>null];
    $players[3] = [ 'addres'=>null, 'name'=>'player4', 'id'=>'playerBottom', 'divId'=>'blackHollBottom','top'=>false, 'down'=>false, 'hels' =>3,'index'=>3, 'first'=>false, 'status'=>null];
    $coords=[4];
    $waiting=0;
    $actualPlay=0;
    $firstPlayer=null;
    $coords['blackHollLeft']=null;
    $coords['blackHollRight']=null;
    $coords['blackHollTop']=null;
    $coords['blackHollBottom']=null;
    $w;$a;$s;$d;
    $status='wait';
    $start =false;
    $ws_worker->count = 1;
    $connect=null;
    $cislo=0;
    $count=0;
    $timerId=null;

    

    function ballCoord($ws_worker) {
        $GLOBALS['x']=$GLOBALS['x']+$GLOBALS['speedX']*$GLOBALS['timer'];
        $GLOBALS['y']=$GLOBALS['y']+$GLOBALS['speedY']*$GLOBALS['timer'];
        $miss=false;
        $a=[];
        $a[0]='x';
        $a[1]='y';
        $strany=[];
        $index=4;
        $strany[0]=($GLOBALS['coords']['blackHollLeft']!=null)? [[0,10],[$GLOBALS['coords']['blackHollLeft']-3,$GLOBALS['coords']['blackHollLeft']+30],[90,100]]:[[4.5,100]];      
        $strany[1]=($GLOBALS['coords']['blackHollRight']!=null)? [[0,10],[$GLOBALS['coords']['blackHollRight']-3,$GLOBALS['coords']['blackHollRight']+30],[90,100]]:[[4.5,100]];    
        $strany[2]=($GLOBALS['coords']['blackHollTop']!=null)? [[0,10],[$GLOBALS['coords']['blackHollTop']-3,$GLOBALS['coords']['blackHollTop']+30],[90,100]]:[[4.5,100]];    
        $strany[3]=($GLOBALS['coords']['blackHollBottom']!=null)? [[0,10],[$GLOBALS['coords']['blackHollBottom']-3,$GLOBALS['coords']['blackHollBottom']+30],[90,100]]:[[4.5,100]];    
        
        if ($GLOBALS['x']<=4.5&&$GLOBALS['speedX']<0){
            $index=0;
            $GLOBALS['speedX']*=-1;
        }
        else if ($GLOBALS['x']+3>=96.5&&$GLOBALS['speedX']>0){
            $index=1;
            $GLOBALS['speedX']*=-1;
        }
        else if ($GLOBALS['y']<=4.5&&$GLOBALS['speedY']<0){
            $index=2;
            $GLOBALS['speedY']*=-1;
        }
        else if ($GLOBALS['y']+3>=96.5&&$GLOBALS['speedY']>0){
            $index=3;
            $GLOBALS['speedY']*=-1;
        }
            if($index!=4){
            $miss=true;
            foreach($strany[$index] as $stran){
                if (($stran[0]<$GLOBALS[$a[intdiv(($index+2)%4,2)]])&&($stran[1]>$GLOBALS[$a[intdiv(($index+2)%4,2)]])){
                    if($GLOBALS['players'][$index]['top']=='true'){
                        if($GLOBALS['speed'.strtoupper($a[intdiv(($index+2)%4,2)])]>0){
                            $GLOBALS['speed'.strtoupper($a[intdiv(($index+2)%4,2)])]-=2;
                            $GLOBALS['speed'.strtoupper($a[intdiv($index,2)])]+=2;
                        }
                        else if($GLOBALS['speed'.strtoupper($a[intdiv(($index+2)%4,2)])]<0){
                            $GLOBALS['speed'.strtoupper($a[intdiv(($index+2)%4,2)])]+=2;
                            $GLOBALS['speed'.strtoupper($a[intdiv($index,2)])]-=2;
                        }
                        else{
                            $GLOBALS['speed'.strtoupper($a[intdiv(($index+2)%4,2)])]=-1*$GLOBALS['speed'.strtoupper($a[intdiv($index,2)])]/2;
                            $GLOBALS['speed'.strtoupper($a[intdiv($index,2)])]/=2;
                        }
                    }
                    else if($GLOBALS['players'][$index]['down']=='true'){
                        if($GLOBALS['speed'.strtoupper($a[intdiv(($index+2)%4,2)])]>0){
                            $GLOBALS['speed'.strtoupper($a[intdiv(($index+2)%4,2)])]+=2;
                            $GLOBALS['speed'.strtoupper($a[intdiv($index,2)])]-=2;
                        }
                        else if($GLOBALS['speed'.strtoupper($a[intdiv(($index+2)%4,2)])]<0){
                            $GLOBALS['speed'.strtoupper($a[intdiv(($index+2)%4,2)])]-=2;
                            $GLOBALS['speed'.strtoupper($a[intdiv($index,2)])]+=2;
                        }
                        else{
                            $GLOBALS['speed'.strtoupper($a[intdiv(($index+2)%4,2)])]=$GLOBALS['speed'.strtoupper($a[intdiv($index,2)])]/2;
                            $GLOBALS['speed'.strtoupper($a[intdiv($index,2)])]/=2;
                        }
                    }
                    else if(abs($GLOBALS['speedX'])<0.1 && count($strany[$index])==1){
                        $GLOBALS['speedY']/=2;
                        $GLOBALS['speedX']=$GLOBALS['speedY'];
                        echo(count($strany[$index]));
                    }
                    else if(abs($GLOBALS['speedY'])<0.1 && count($strany[$index])==1){
                        $GLOBALS['speedX']/=2;
                        $GLOBALS['speedY']=$GLOBALS['speedX'];
                        echo(count($strany[$index]));

                    }
                    $GLOBALS['count']+=1;
                    if (($GLOBALS['count']+1)%20==0){
                        $GLOBALS['speed']+=2;
                    }
                    return;
                }
            } 
        }
          if ($miss){
            $GLOBALS['players'][$index]['hels']-=1;
            if ($GLOBALS['players'][$index]['hels']==0){
                
                foreach($ws_worker->connections as $connection)
            {
                $address = "{$connection->getRemoteIp()}:{$connection->getRemotePort()}";
                if($address==$GLOBALS['players'][$index]['addres']){
                    $obj = new stdClass();
                    $obj->status='lose';
                    $connection->send(json_encode($obj));
                    close($connection);
                }
            }
                $GLOBALS['players'][$index]['addres']=null;
                
            }
            $GLOBALS['angel']=rand(0,360);
            $GLOBALS['speedX']=$GLOBALS['speed']*sin(deg2rad($GLOBALS['angel']));
            $GLOBALS['speedY']=$GLOBALS['speed']*cos(deg2rad($GLOBALS['angel']));;
            $GLOBALS['x']=48.5;
            $GLOBALS['y']=48.5;
          }
	}
    $ws_worker->count = 1;
    
    // Add a Timer to Every worker process when the worker process start
    $ws_worker->onWorkerStart = function($ws_worker)
    {   
    //Timer every 5 seconds
        
    
    
 
    // Emitted when new connection come
    $ws_worker->onConnect = function($connection)
    {
        // $GLOBALS['players'][]=[$connection,$GLOBALS['cislo']];
        // $GLOBALS['cislo']++;
        // Emitted when websocket handshake done
        $connection->onWebSocketConnect = function($connection)
        {
            $address = "{$connection->getRemoteIp()}:{$connection->getRemotePort()}";
                    //$GLOBALS['players'][$i]['addres']=$address;
                    $obj = new stdClass();
                    if($GLOBALS['firstPlayer']==null){
                        $GLOBALS['firstPlayer']=$address;
                        $GLOBALS['players'][0]['addres']=$address;
                        $GLOBALS['coords'][$GLOBALS['players'][0]['divId']]=35;
                        $GLOBALS['players'][0]['first']='true';
                        $GLOBALS['players'][0]['status']='wait';
                        $obj->first='true';
                        $GLOBALS['waiting']++;

                    }
                    else{
                        $obj->first='false';
                    }
                    $obj->status = 'wait';
                    $obj->waiting = $GLOBALS['waiting']-$GLOBALS['actualPlay'];
                    $connection->send(json_encode($obj));
                    return;
        };
    };
    
    $ws_worker->onMessage = function($connection, $data)
    {
        // $GLOBALS['userdata']=$data;
        $address = "{$connection->getRemoteIp()}:{$connection->getRemotePort()}";
        $data=json_decode($data,true);
        if($data[0]['status']=='wait'){
            if ($GLOBALS['waiting']<4){
            for($i=0;$i<4;$i++){
                if($GLOBALS['players'][$i]['addres']==null){
                    $GLOBALS['players'][$i]['addres']=$address;
                    $GLOBALS['players'][$i]['name']=$data[0]['name'];
                   //$GLOBALS['coords'][$GLOBALS['players'][$i]['divId']]=35;
                    if ($address == $GLOBALS['firstPlayer']){
                        $GLOBALS['players'][$i]['first']='true';
                    }
                    $GLOBALS['players'][$i]['status']='wait';
                    break;
                }
            }
            $GLOBALS['waiting']++;
            if ($GLOBALS['waiting']==4){
                if(!$GLOBALS['start']){
                    play();
                    return;
                }
                
            }
                foreach($GLOBALS['ws_worker']->connections as $connection)
                {
                    $addr2 = "{$connection->getRemoteIp()}:{$connection->getRemotePort()}";
                    for($i=0;$i<4;$i++){
                        if($GLOBALS['players'][$i]['addres']==$addr2){
                            if($GLOBALS['players'][$i]['status']=='wait'){
                                $obj = new stdClass();
                                $obj->first =$GLOBALS['players'][$i]['first'];
                                $obj->status = 'wait';
                                $obj->waiting = ($GLOBALS['waiting']-$GLOBALS['actualPlay']);
                                $connection->send(json_encode($obj));
                            }

                        }
                    }
                }
        }
        
        else{
            $connection->close();
        }

       }


        if ($GLOBALS['start']){
            for($i=0;$i<4;$i++){
                if($GLOBALS['players'][$i]['addres']==$address){
                    if($GLOBALS['players'][$i]['status']=='run'){
                        $GLOBALS['players'][$i]['name']=$data[0]['name'];
                        $GLOBALS['players'][$i]['top']=$data[0]['top'];
                        $GLOBALS['players'][$i]['down']=$data[0]['down'];
                        $GLOBALS['coords'][$GLOBALS['players'][$i]['divId']]=$data[0]['coord'];
                        return;
                    }
                }
            }       
        }  

        if($data[0]['status']=='play'){
            for($z=0;$z<4;$z++){
                if($GLOBALS['players'][$z]['addres']==$address){
                    $GLOBALS['players'][$z]['name']=$data[0]['name'];
                }}
            play();  
        }

    };
    function play(){
        if($GLOBALS['waiting']==0){
            return;
        }
        $GLOBALS['actualPlay']=$GLOBALS['waiting'];
        $GLOBALS['start']=true;
        $GLOBALS['status']='run';
        for($i=0;$i<4;$i++){
            if($GLOBALS['players'][$i]['status']=='wait'){
                $GLOBALS['players'][$i]['status']='run';
            }
        }  
        $GLOBALS['count']=0;
        $GLOBALS['speed']=8;
        $GLOBALS['angel']=rand(0,360);
        $GLOBALS['speedY']=$GLOBALS['speed']*sin(deg2rad($GLOBALS['angel']));
        $GLOBALS['speedX']=$GLOBALS['speed']*cos(deg2rad($GLOBALS['angel']));;
        $GLOBALS['x']=48.5;
        $GLOBALS['y']=48.5;
        $GLOBALS['timerId']=Timer::add(0.01,function() {
                ballCoord($GLOBALS['ws_worker']); 
                foreach($GLOBALS['ws_worker']->connections as $connection)
                {
                    $address = "{$connection->getRemoteIp()}:{$connection->getRemotePort()}";
                    foreach($GLOBALS['players'] as $player){
                        if($player['addres']==$address){
                            if($player['status']=='run'){
                            $obj = new stdClass();
                            $obj->id = $player['id'];
                            $obj->ballX = $GLOBALS['x'];
                            $obj->ballY = $GLOBALS['y'];
                            $obj->divId = $player['divId'];
                            $obj->coords = $GLOBALS['coords'];  
                            $obj->count = $GLOBALS['count'];
                            $obj->players = $GLOBALS['players'];
                            $obj->index = $player['index'];
                            $obj->status='run';
                            $connection->send(json_encode($obj));
                            }
                        }
                }
                } 
        });
    }
    // Emitted when connection closed
    $ws_worker->onClose = function($connection)
    {
        close($connection);
    };

    function close($connection){
        $address = "{$connection->getRemoteIp()}:{$connection->getRemotePort()}";
        for($i=0;$i<4;$i++){
            if($GLOBALS['players'][$i]['addres']==$address){
                $GLOBALS['waiting']--;
                if($GLOBALS['players'][$i]['status']=='run'){
                    $GLOBALS['actualPlay']--;
                }
                $GLOBALS['players'][$i]['addres']=null;
                if($address==$GLOBALS['firstPlayer']){
                    $GLOBALS['firstPlayer']=null;
                    for($k=0;$k<4;$k++){
                        if($GLOBALS['players'][$k]['addres']!=null){
                            $GLOBALS['firstPlayer']=$GLOBALS['players'][$k]['addres'];
                            $GLOBALS['players'][$k]['first']=true;
                        }
                    }
                }
                foreach($GLOBALS['ws_worker']->connections as $connection2)
                    {
                    $address2 = "{$connection2->getRemoteIp()}:{$connection2->getRemotePort()}";
                    for($l=0;$l<4;$l++){
                        if($GLOBALS['players'][$l]['addres']==$address2){
                            if($GLOBALS['players'][$l]['status']=='wait'){
                                $obj = new stdClass();
                                $obj->waiting = ($GLOBALS['waiting']-$GLOBALS['actualPlay']);
                                $obj->status = 'wait';
                                $obj->first=$GLOBALS['players'][$l]['first'];
                                $connection2->send(json_encode($obj));
                            }
                            }        
                        }
                    }
                
                $GLOBALS['players'][$i]['name']=null;
                $GLOBALS['players'][$i]['top']=null;
                $GLOBALS['players'][$i]['down']=null;
                $GLOBALS['players'][$i]['first']=false;
                $GLOBALS['players'][$i]['status']=null;
                $GLOBALS['players'][$i]['hels']=3;
                $GLOBALS['coords'][$GLOBALS['players'][$i]['divId']]=null;

                if($GLOBALS['actualPlay']==0){
                    $GLOBALS['status']='wait';
                   // $GLOBALS['actualPlay']=0;
                    $GLOBALS['start']=false;
                    if($GLOBALS['timerId']!=null){
                    Timer::del($GLOBALS['timerId']);
                    $GLOBALS['timerId']=null;
                    }
                    foreach($GLOBALS['ws_worker']->connections as $connection3)
                    {
                        $address3 = "{$connection3->getRemoteIp()}:{$connection3->getRemotePort()}";
                        for($l=0;$l<4;$l++){
                            if($GLOBALS['players'][$l]['addres']==$address3){
                                if($GLOBALS['players'][$l]['status']!=null){
                                    $obj = new stdClass();
                                    $obj->waiting = ($GLOBALS['waiting']-$GLOBALS['actualPlay']);
                                    $obj->status = 'wait';
                                    $obj->first=$GLOBALS['players'][$l]['first'];
                                    $connection2->send(json_encode($obj));
                                }
                            }
                                
                            }        
                        }
                    play();    
                }
            }   
        }
        $connection->close();
        echo "Connection closed";
    }

}; 
    // Run worker
    Worker::runAll();



?>    