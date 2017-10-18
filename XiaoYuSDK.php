<?php
<<<COPYRIGHT

COPYRIGHT;

class XiaoYuSDK
{
    /**
     * HTTP方法：GET
     */
    const METHOD_GET = 'GET';
    /**
     * HTTP方法：POST
     */
    const METHOD_POST = 'POST';
    /**
     * HTTP方法：PUT
     */
    const METHOD_PUT = 'PUT';
    /**
     * HTTP方法：DELETE
     */
    const METHOD_DELETE = 'DELETE';

    /**
     * @var string 企业id
     */
    private $enterprise_id = 'e2b8b52b994d8376ea23b7be4965467423180ae2';
    /**
     * @var string 企业token
     */
    private $token = '45d947778e24ea6af9c99b287b34ead991c87241af426120484b2cac2691d7a2';
    /**
     * @var string API接口地址
     */
    private $api = 'https://cloud.xylink.com/api/rest/external/v1/';

    /**
     * XY_Live constructor.
     */
    public function __construct()
    {
        //$result = $this->get_meeting_status('123456789');
        //var_dump($result);
        //$result = $this->get_live_videos(524209, '910088157943');
        //var_dump($result);
        //$result = $this->get_nemos();
        //var_dump($result);
        //$result = $this->remove_live(524209, 'ff808081581238ae0158298f9aea2d40');
        //var_dump($result);
        //$result = $this->get_live(524209, 'ff808081581238ae015829844fb12c55');
        //var_dump($result);
        //$result = $this->get_live_videos(524209, 'ff808081581238ae01583e454ae32940');
        //var_dump($result);
        $result = $this->get_users();
       	var_dump($result);
        $result = $this->get_nemos();
        var_dump($result);
        $result = $this->create_meeting('Pig Zhu 的会议室', time(), time() + 3600 * 6, 5, TRUE, '123456');
        var_dump($result);
        //$result = $this->create_live(524209, 'Pig Zhu 的直播 ' . date('Y-m-d H:i:s'), '910030701173', time(), time() + 3600 * 6, 'detail', TRUE, TRUE, 'location');
        //var_dump($result);

        $result = $this->register_callback('LiveStatus', 'http://182.61.56.223/debug/');
        var_dump($result);
        $result = $this->get_registered_callbacks();
        var_dump($result);
        $result = $this->create_meeting_reminder('测试会议', time() + 3600 * 2, time() + 3600 * 6, ['234157','524209'], '918626467882', '南京', '1029d', 1, 2);
        var_dump($result);

    }

    /**
     * 创建会议
     * 这个API可以让合作伙伴直接在自己的系统内创建一个云会议号，用户可以选择是否需要密码以及会议的开始时间，结束时间等。
     *
     * @param string      $meeting_name     会议的名称，需要做url encode
     * @param int         $start_time       会议开始时间，时间戳
     * @param int         $end_time         会议结束时间，时间戳
     * @param int         $max_participant  最大参加人数
     * @param bool        $require_password 是否需要密码（true/false），如果为true，则会默认生成一个6位数字密码；如为false，则该会议没有密码
     * @param null|string $password         指定密码，可选参数。如果用户指定了密码，那么该会议默认有密码。如果用户没有指定密码，但是require_password为true，小鱼会自动生成密码
     * @param null|int    $meetingNumber    用户可指定会议室号，规则为9100开头总长度为12的数字，如果不指定，小鱼自动生成云会议室号
     * @return array
     */
    public function create_meeting($meeting_name, $start_time, $end_time, $max_participant, $require_password = FALSE, $password = NULL, $meetingNumber = NULL)
    {
        $data = get_defined_vars();
        $data['start_time'] *= 1000;
        $data['end_time'] *= 1000;
        $uri    = 'create_meeting';
        $result = $this->send($uri, self::METHOD_GET, $data);
        return $result;
    }

    /**
     * 预约直播
     * 从第三方后台预约直播。
     *
     * @param int         $nemoNumber           小鱼号
     * @param string      $title                直播标题 （必需,长度不超过32)
     * @param int         $confNo               云会议室号 （必需)
     * @param int         $startTime            直播开始时间（必需,必需在当前时间之后)
     * @param int         $endTime              直播结束时间 （必需,必需在开始时间之后)
     * @param string      $detail               直播详情 （可选,长度不超过128）
     * @param bool        $autoRecording        是否自动录制（必需,true/false.如果是false，则不会自动发布录制。只有设置为true时autoPublishRecording才可以设置为true）
     * @param bool        $autoPublishRecording 是否自动发布录制（必需,true/false。只有设置为true，才能获取直播的回放列表）
     * @param null|string $location             直播地点（可选,长度不超过64）
     * @return array
     */
    public function create_live($nemoNumber, $title, $confNo, $startTime, $endTime, $detail = '', $autoRecording, $autoPublishRecording, $location = NULL)
    {
        $data = get_defined_vars();
        $data['startTime'] *= 1000;
        $data['endTime'] *= 1000;
        $uri    = 'liveVideo2/enterprise/' . $this->enterprise_id . '/xiaoyunumber/' . $nemoNumber . '/live';
        $result = $this->send($uri, self::METHOD_POST, $data);
        return $result;
    }

    /**
     * 修改直播
     * 从第三方后台更新一条现有未开始的直播。
     *
     * @param int         $nemoNumber           小鱼号
     * @param string      $live_id              直播号
     * @param string      $title                直播标题 （必需,长度不超过32)
     * @param int         $confNo               云会议室号 （必需)
     * @param int         $startTime            直播开始时间（必需,必需在当前时间之后)
     * @param int         $endTime              直播结束时间 （必需,必需在开始时间之后)
     * @param string      $detail               直播详情 （可选,长度不超过128）
     * @param bool        $autoRecording        是否自动录制（必需,true/false.如果是false，则不会自动发布录制。只有设置为true时autoPublishRecording才可以设置为true）
     * @param bool        $autoPublishRecording 是否自动发布录制（必需,true/false。只有设置为true，才能获取直播的回放列表）
     * @param null|string $location             直播地点（可选,长度不超过64）
     * @return array
     */
    public function update_live($nemoNumber, $live_id, $title, $confNo, $startTime, $endTime, $detail = '', $autoRecording, $autoPublishRecording, $location = NULL)
    {
        $data = get_defined_vars();
        $data['startTime'] *= 1000;
        $data['endTime'] *= 1000;
        $uri    = 'liveVideo2/enterprise/' . $this->enterprise_id . '/xiaoyunumber/' . $nemoNumber . '/live/' . $live_id;
        $result = $this->send($uri, self::METHOD_PUT, $data);
        return $result;
    }

    /**
     * 删除直播
     * 从第三方后台同步删除一条存在的直播。会删除和这个直播相关的所有信息，包括但不限于回放列表。
     *
     * @param int    $nemoNumber 小鱼号
     * @param string $live_id    直播号
     * @return array
     */
    public function remove_live($nemoNumber, $live_id)
    {
        $uri    = 'liveVideo2/enterprise/' . $this->enterprise_id . '/xiaoyunumber/' . $nemoNumber . '/live/' . $live_id;
        $result = $this->send($uri, self::METHOD_DELETE);
        return $result;
    }

    /**
     * 获取小鱼上的某个直播
     *
     * @param int    $nemoNumber 小鱼号
     * @param string $live_id    直播号
     * @return array
     */
    public function get_live($nemoNumber, $live_id)
    {
        $uri    = 'liveVideo2/enterprise/' . $this->enterprise_id . '/xiaoyunumber/' . $nemoNumber . '/live/' . $live_id;
        $result = $this->send($uri, self::METHOD_GET);
        return $result;
    }

    /**
     * 获取某个直播的视频列表
     * 删除直播会删除视频列表。预约直播或修改直播时autoRecording和autoPublishRecording必须都为true才有视频列表。
     *
     * @param int    $nemoNumber 小鱼号
     * @param string $live_id    直播号
     * @return array
     */
    public function get_live_videos($nemoNumber, $live_id)
    {
        $uri    = 'liveVideo2/enterprise/' . $this->enterprise_id . '/xiaoyunumber/' . $nemoNumber . '/live/' . $live_id . '/videos';
        $result = $this->send($uri, self::METHOD_GET);
        return $result;
    }

    /**
     * 获取通讯录中所有员工
     * 获取企业下所有员工。
     *
     * @return array
     */
    public function get_users()
    {
        $uri    = 'buffet/user';
        $result = $this->send($uri, self::METHOD_GET);
        return $result;
    }

    /**
     * 添加员工的企业通讯录
     * 添加员工到指定的企业通讯录。
     *
     * @param string $phone       手机号
     * @param string $name        姓名
     * @param string $countryCode 国家码
     * @return array
     */
    public function create_user($phone, $name, $countryCode = '+86')
    {
        $data   = get_defined_vars();
        $uri    = 'buffet/user';
        $result = $this->send($uri, self::METHOD_POST, $data);
        return $result;
    }

    /**
     * 修改企业通讯录员工属性
     * 根据传入的countryCode和phone修改员工的基本属性(目前只支持修改name)。
     *
     * @param string $phone       手机号
     * @param string $name        姓名
     * @param string $countryCode 国家码
     * @return array
     */
    public function update_user($phone, $name, $countryCode = '+86')
    {
        $data   = get_defined_vars();
        $uri    = 'buffet/user';
        $result = $this->send($uri, self::METHOD_PUT, $data);
        return $result;
    }

    /**
     * 删除员工的企业通讯录
     * 根据countryCode和phone删除员工。
     *
     * @param string $phone       手机号
     * @param string $countryCode 国家码
     * @return array
     */
    public function remove_user($phone, $countryCode = '+86')
    {
        $data   = get_defined_vars();
        $uri    = 'buffet/user';
        $result = $this->send($uri, self::METHOD_DELETE, $data);
        return $result;
    }

    /**
     * 获取企业通讯录小鱼
     *
     * @return array
     */
    public function get_nemos()
    {
        $uri    = 'buffet/nemos';
        $result = $this->send($uri, self::METHOD_GET);
        return $result;
    }

    /**
     * 创建预约会议记录
     * 从第三方后台同步预约一条新的会议信息。
     *
     * @param string $meetingId     会议ID，唯一标示一条第三方预约会议记录，由第三方创建
     * @param int    $meetingName   会议标题
     * @param int    $startTime     会议开始时间
     * @param int    $endTime       会议结束时间
     * @param array  $participants  所有与会小鱼的小鱼号
     * @param int    $reminder      预先提醒时间
     * @param string $meetingRoomId 虚拟会议室号
     * @param string $password      会议密码（长度不超过6）
     * @return array
     */
    public function create_scheduled_meetings($meetingId, $meetingName, $startTime, $endTime, $participants, $reminder, $meetingRoomId, $password)
    {
        $data = get_defined_vars();
        $data['startTime'] *= 1000;
        $data['endTime'] *= 1000;
        $data['reminder'] *= 1000;
        $uri                  = 'scheduledMeetings';
        $result               = $this->send($uri, self::METHOD_POST, $data, TRUE);
        return $result;
    }

    public function get_scheduled_meetings()
    {
        $uri    = 'scheduledMeetings';
        $result = $this->send($uri, self::METHOD_GET, TRUE);
        return $result;
    }

    /**
     * 获得企业一天内的视频列表
     * startTime和endTime为获取视频列表的时间区间，都必须大于0，endTime必须大于startTime，且时间范围不能超过24小时。
     *
     * @param int $startTime 开始时间
     * @param int $endTime   结束时间
     * @return array
     */
    public function get_vods($startTime, $endTime)
    {
        $data = get_defined_vars();
        $data['startTime'] *= 1000;
        $data['endTime'] *= 1000;
        $uri                  = 'vods';
        $result               = $this->send($uri, self::METHOD_GET, $data, TRUE);
        return $result;
    }

    /**
     * 获得某个视频的默认缩略图
     *
     * @param string $vodId 视频ID
     * @return array
     */
    public function get_vod_thumb($vodId)
    {
        $data                 = array();
        $uri                  = 'vods/' . $vodId . '/thumbnail';
        $result               = $this->send($uri, self::METHOD_GET, $data, TRUE);
        return $result;
    }

    /**
     * 下载一个视频
     *
     * @param string $vodId 视频ID
     * @return array
     */
    public function download_vod($vodId)
    {
        $data                 = array();
        $uri                  = 'vods/' . $vodId . '/download';
        $result               = $this->send($uri, self::METHOD_GET, $data, TRUE);
        return $result;
    }

    /**
     * 删除一个视频
     *
     * @param string $vodId 视频ID
     * @return array
     */
    public function remove_vod($vodId)
    {
        $data                 = array();
        $uri                  = 'vods/' . $vodId;
        $result               = $this->send($uri, self::METHOD_DELETE, $data, TRUE);
        return $result;
    }

    /**
     * 获得企业某个小鱼的视频列表
     * startTime和endTime为获取视频列表的时间区间，为可选参数，如果startTime大于0，那么endTime必须大于startTime。如果startTime没有值或者小于等于0，则返回这个小鱼的所有视频。
     *
     * @param int $nemoNumber 小鱼号
     * @param int $startTime  开始时间
     * @param int $endTime    结束时间
     * @return array
     */
    public function get_nemo_vods($nemoNumber, $startTime = 0, $endTime = 0)
    {
        $data = get_defined_vars();
        $data['startTime'] *= 1000;
        $data['endTime'] *= 1000;
        $uri                  = 'nemo/' . $nemoNumber . '/vods';
        $result               = $this->send($uri, self::METHOD_GET, $data, TRUE);
        return $result;
    }

    /**
     * 获得企业某个会议室的视频列表
     * startTime和endTime为获取视频列表的时间区间，为可选参数，如果startTime大于0，那么endTime必须大于startTime。如果startTime没有值或者小于等于0，则返回这个小鱼的所有视频。
     *
     * @param string $meetingRoomNumber 会议室号码
     * @param int    $startTime         开始时间
     * @param int    $endTime           结束时间
     * @return array
     */
    public function get_meetingroom_vods($meetingRoomNumber, $startTime = 0, $endTime = 0)
    {
        $data = get_defined_vars();
        $data['startTime'] *= 1000;
        $data['endTime'] *= 1000;
        $data['enterpriseId'] = $this->enterprise_id;
        $uri                  = 'meetingroom/' . $meetingRoomNumber . '/vods';
        $result               = $this->send($uri, self::METHOD_GET, $data);
        return $result;
    }

    /**获得所有注册的回调
     * @return array
     */

    public function get_registered_callbacks()
    {
        $uri                  = 'callbacks';
        $result               = $this->send($uri, self::METHOD_GET, NULL);
        return $result;
    }

    /**注册回调
     * @param $callbackEvent 回调类型,NemoUnbound/LiveStatus/NewCallPush/NewUserCall
     * @param $handlerUrl 回调的URL
     * @return array
     */
    public function register_callback($callbackEvent, $handlerUrl)
    {
        $data = get_defined_vars();
        $uri                  = 'callbacks';
        $result               = $this->send($uri, self::METHOD_POST, $data, TRUE);
        return $result;
    }

    /**
     * @param $title
     * @param $startTime
     * @param $endTime
     * @param $participants
     * @param $conferenceNumber
     * @param $address
     * @param $details
     * @param $autoInvite
     * @param $meetingRoomType
     * @return array
     */
    public function create_meeting_reminder($title, $startTime, $endTime, $participants, $conferenceNumber, $address, $details, $autoInvite, $meetingRoomType)
    {
        $data = get_defined_vars();
        var_dump($data);
        $data['startTime'] *= 1000;
        $data['endTime'] *= 1000;
        $uri                  = 'meetingreminders';
        $result               = $this->send($uri, self::METHOD_POST, $data, TRUE);
        return $result;
    }

    /**
     * @param $title
     * @param $startTime
     * @param $endTime
     * @param $participants
     * @param $conferenceNumber
     * @param $address
     * @param $details
     * @param $autoInvite
     * @return array
     */

    public function update_meeting_reminder($title, $startTime, $endTime, $participants, $conferenceNumber, $address, $details, $autoInvite)
    {
        $data = get_defined_vars();
        var_dump($data);
        $data['startTime'] *= 1000;
        $data['endTime'] *= 1000;
        $uri                  = 'meetingreminders';
        $result               = $this->send($uri, self::METHOD_PUT, $data, TRUE);
        return $result;
    }


    /*public function get_meeting_status($meetingRoomNumber)
    {
        $data                 = array();
        $data['enterpriseId'] = $this->enterprise_id;
        $uri                  = 'conferenceControl/' . $meetingRoomNumber . '/meetingStatus';
        $result               = $this->send($uri, self::METHOD_GET, $data);
        return $result;
    }*/

    /**
     * 发送接口数据
     *
     * @param string     $uri    API的URI路径
     * @param string     $method 请求方法
     * @param null|array $param  请求参数
     * @param bool       $eid_fix enterpriseId形式
     * @return array
     */
    public function send($uri, $method, $param = NULL, $eid_fix = FALSE)
    {
        if ($eid_fix)
            $query['enterpriseId'] = $this->enterprise_id;
        else
            $query['enterprise_id'] = $this->enterprise_id;

        $query['signature']     = $this->signature($uri, $method, $param, $eid_fix);

        $query = http_build_query($method == self::METHOD_GET ? array_merge($param, $query) : $query);
        $url   = $this->api . $uri . '?' . $query;
        $result = $this->request($url, $method, $param);
        return $result;
    }

    /**
     * 生成签名
     *
     * @param string     $uri    API的URI路径
     * @param string     $method 请求方法
     * @param null|array $param  请求参数
     * @return string
     */
    private function signature($uri, $method, &$param = NULL, $eid_fix = false)
    {
        $param = empty($param) || !is_array($param) ? array() : $param;
        foreach ($param as $key => $val)
        {
            if (is_null($val)) unset($param[$key]);
        }

        $entity_data = '';
        $sign_param  = NULL;
        if ($eid_fix)
            $sign_param = array('enterpriseId' => $this->enterprise_id);
        else
            $sign_param = array('enterprise_id' => $this->enterprise_id);

        if (strtoupper($method) == self::METHOD_GET)
        {
            $sign_param = array_merge($param, $sign_param);
        }
        else
        {
            $entity_data = mb_substr(json_encode($param), 0, 100, 'UTF-8');
        }

        ksort($sign_param);
        $entity = hash('SHA256', $entity_data, TRUE);
        $entity = base64_encode($entity);
        
	    $sign   = $method . "\n" . $uri . "\n" . http_build_query($sign_param) . "\n" . $entity;
        $res    = hash_hmac('SHA256', $sign, $this->token, TRUE);
        $res    = base64_encode($res);
        $res    = str_replace(' ', '+', $res);

        return $res;
    }

    /**
     * 请求API接口
     *
     * @param string            $url    API地址
     * @param string            $method 请求方法
     * @param null|string|array $data   请求参数
     * @return array
     */
    private function request($url, $method, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); //设置GET的URL地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);//将结果保存成字符串
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);//连接超时时间s
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);//执行超时时间s
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 1800);//DNS解析缓存保存时间半小时
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, 0);//丢掉头信息

        if (strtoupper($method) != self::METHOD_GET)
        {
            $data = is_string($data) ? $data : json_encode($data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $content   = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        unset($ch);
        var_dump($content);
        return $this->decode_result($content, $http_code);
    }

    /**
     * 解析返回值
     *
     * @param string $content   返回内容
     * @param int    $http_code 返回HTTP状态码
     * @return array
     */
    private function decode_result($content, $http_code)
    {
        $result = array(
            'status'     => FALSE,
            'msg'        => 'Request failure',
            'error_code' => NULL,
            'http_code'  => intval($http_code),
            'info'       => array(),
        );

        $content = empty($content) ? array() : json_decode($content, TRUE);
        if (!empty($content))
        {
            if (!empty($content['errorCode']))
            {

                isset($content['userMessage']) && $result['msg'] = $content['userMessage'];
                isset($content['errorCode']) && $result['error_code'] = $content['errorCode'];
            }
            else
            {
                $result['status']     = TRUE;
                $result['msg']        = 'ok';
                $result['error_code'] = 0;
                $result['info']       = $content;
            }
        }
        else
        {
            if ($http_code == 200 || $http_code == 204)
            {
                $result['status']     = TRUE;
                $result['msg']        = 'ok';
                $result['error_code'] = 0;
            }
        }
        return $result;
    }
}
