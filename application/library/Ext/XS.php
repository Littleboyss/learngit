<?php 
/**
 * Created by Colgate
 */
class Ext_XS extends \XS
{
    static $xsDocument = null;
    protected $data = [];
    
    private $sortMap = [
        SORT_ASC => true,
        SORT_DESC => false,
    ];
    
    private $blackList = [
        '公司',
        '市',
    ];
	/**
	 * 构造函数
	 */
	public function __construct($projectName)
	{
        parent::__construct(DOC_ROOT . "/conf/xs_$projectName.ini");
	}
    
    /**
     * 将准备数据简单封装，统一入口，便于业务变动
     */
    public function add($data)
    {
        if(!count($data)){
            return false;
        }
        
        $this->getDoc()->setFields($data);
        
        $this->index->update($this->getDoc());
    }
    
    /**
     * 静态获取XSDocument对象
     */
    public function getDoc()
    {
        if(is_null(self::$xsDocument)){
            self::$xsDocument = new XSDocument;
        }
        return self::$xsDocument;
    }
    
    /**
     * 将XSSearch的search方法简单封装，便于业务
     * @param keywords 关键字
     */
    public function search($keywords = '', $page = 1, $psize = 10)
    {
        // 分词搜索
        if(!$keywords){
            return [];
        }
        $origin = $keywords;
        
        $keywords = $this->fuzzy($origin);
        
        $formatKeywords = mb_substr(implode(' OR ', (new XSTokenizerScws)->getTokens($keywords)), 0, 70);
        $formatKeywords = rtrim(rtrim(rtrim($formatKeywords, ' O'), ' OR'), ' OR ');
        
        $limit = max(($page - 1), 0) * $psize;
        
        $this->search->setQuery($formatKeywords);
        $this->search->setLimit($psize, $limit);
        
        $retval['rows'] = $this->search->search();
        $retval['total'] = $this->search->count($formatKeywords);
        
        if($retval['total'] == 0){
            $this->saveNoResultOrigin($origin);
        }
        
        return $retval;
    }
    
    /**
     * 将没有搜到的关键字存储起来
     * @param keywords 关键字
     */
    private function fuzzy($origin)
    {
        $keywords = $origin;
        $synonyms = $this->search->getAllSynonyms();
        if(isset($synonyms[$origin])){
            $keywords = implode($synonyms[$origin]);
        }
        $expands = $this->search->getExpandedQuery($origin);
        if(count($expands) > 0){
            $keywords .= implode($expands);
        }
        return str_replace($this->blackList, '', $keywords);
    }
    /**
     * 将没有搜到的关键字存储起来
     * @param keywords 关键字
     */
    private function saveNoResultOrigin($keywords)
    {
        $cache = E_Cache::init('redis');
        
        $noresult = $cache->conn->get('xssearch:noresult');
        if(!$noresult){
            $noresult = [];
        }else{
            $noresult = json_decode($noresult, true);
        }
        if(in_array($keywords, $noresult)){
            return true;
        }
        $noresult[] = $keywords;
        
        $cache->conn->set('xssearch:noresult', json_encode($noresult, JSON_UNESCAPED_UNICODE));
        
        return true;
    }
    
    /**
     * 删除无结果关键字
     * @param keywords 关键字
     */
    public function removeNoResultOrigin($keywords)
    {
        $cache = E_Cache::init('redis');
        
        $noresult = $cache->conn->get('xssearch:noresult');
        if(!$noresult){
            $noresult = [];
        }else{
            $noresult = json_decode($noresult, true);
        }
        
        if(in_array($keywords, $noresult)){
            $newresult = array_filter($noresult, function($v) use ($keywords){ 
                return $v !=  $keywords;
            });
            $cache->conn->set('xssearch:noresult', json_encode($newresult, JSON_UNESCAPED_UNICODE));
        }
        
        return true;
    }
    
    /*
     * 设置排序
        @param sort : [
            'sales_volume' => SORT_DESC // 销量倒序
        ],
     *
     */
    public function setSort( $sort)
    {
        foreach($sort as $k => $v){
            $this->search->setSort($k, $this->sortMap[$v] ?? false);
        }
        return $this;
    }
    
    /*
     * 摘取字段
        @param data : [
            'id' => 123
        ],
     *
     */
    public function pickColumn($data, $field)
    {
        $retval = [];
        foreach($data as $v){
            $retval[] = $v[$field];
        }
        return $retval;
    }
    
    
}