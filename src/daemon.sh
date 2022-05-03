#!/bin/bash

#php运行脚本
PHP="/usr/bin/php"
#项目目录
WWWROOT="/data/wwwroot/laravel"
SCRIPT="artisan"
SERVICE_NAME="hprose:socket"
PID="${WWWROOT}/vendor/${SERVICE_NAME}.pid"
LOG="${WWWROOT}/storage/logs/${SERVICE_NAME}.log"

#判断程序是否已经在运行
status_script(){
    ps -fe|grep ${SCRIPT}|grep ${SERVICE_NAME}|grep -v grep
    if [[ $? -eq 0 ]]
    then
        echo ${0}' Is running'
        running=1
    elif [ $? -ne 0 ]
    then
        echo $0" is NOT running"
        running=2
    fi
}

#启动脚本，先判断脚本是否已经在运行
start_script(){
status_script
    if [[ ${running} -eq 1 ]]
    then
        echo ${0}' Is starting ...'
    else
        echo 'start' ${0} '...'
        cd ${WWWROOT}
#        nohup ${PHP} ${SCRIPT} ${SERVICE_NAME}>/dev/null 2>${LOG} &
        nohup ${PHP} ${SCRIPT} ${SERVICE_NAME} >${LOG} &
        echo $! > ${PID}
        echo "start finish,PID $!"
    fi
}

#停止脚本
stop_script(){
status_script
    if [[ ${running} -ne 1 ]]
    then
        echo ${0}' no starting '$?...
    else
    PIDS=`ps aux|grep ${SCRIPT}|grep ${SERVICE_NAME}| grep -v grep |awk '{print $2}'`
       for kill_pid in ${PIDS}
       do
            kill -TERM ${kill_pid} >/dev/null 2>&1
            echo "Kill pid ${kill_pid} .."
       done
       echo 'stop complete'
       return 1
    fi
}

#重启脚本
reload_script(){
    stop_script
    sleep 3
    start_script
}

# 入口程序
if [[ $# -eq 1 ]]
then
    case $1 in
    start)
        start_script
        ;;
    stop)
        stop_script
        ;;
    status)
        status_script
        ;;
    reload)
        reload_script
        ;;
    restart)
        reload_script
        ;;
    *)
        echo 'SERVER IS '${0} 'status|start|stop|restart';
        ;;
    esac
else
    echo 'SERVER IS '${0} 'status|start|stop|restart';
fi
