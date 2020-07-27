<?php
namespace Report\Controller;
 // 命名空间
use Common\Controller\BaseController;

class ReportController extends BaseController // 继承Controller类
{
    // 绩效汇总
    public function report_oaworkplan_perf_index()
    {
        $party_id = I('get.party_id');
        if (! empty($party_id)) {
            $this->assign('party_id', $party_id);
        }
        $industry = getConfig('industry');
        $this->assign('industry', $industry);
		$db_party = M('ccp_party');
        $party_list = $db_party->select();
		$this->assign('party_list', $party_list);
        $this->display("Rc/report_oaworkplan_perf_index");
    }
    // 绩效汇总数据加载
    public function report_oaworkplan_perf_index_data()
    {
        $party_id = I('get.party_id');
        $where = "status='1'";
        if (! empty($party_id)) {
            $where = "status='1' and party_no='$party_id'";
        }
		$perf_list=array();
        $oa_workplan = M('oa_workplan');
        $ccp_communist = M('ccp_communist');
        $bd_code = M('bd_code');
        $oa_workplan_perf = M('oa_workplan_perf');
        $communist_list = $ccp_communist->where($where)->select();
        $code_list = $bd_code->where("code_group='perf_light'")->select();
        foreach ($communist_list as &$communist) {
            $flag = $oa_workplan_perf->where("perf_communist=" . $communist['communist_no'])->count();
            $score = 0;
            if ($flag > 0) {
                foreach ($code_list as & $code) {
                    $light = $code['code_no'];
                    $perf_count = $oa_workplan_perf->where("perf_light='$light' and perf_communist=" . $communist['communist_no'])->count();
                    if ($perf_count > 0) {
                        $score += $code['memo'] * $perf_count;
                    }
                }
                $communist['party_name'] = getPartyInfo($communist['party_no']);
                $communist['post_name'] = getPartydutyInfo($communist['communist_post_no']);
                $communist['score'] = $score;
                $communist['operate'] = "<a class='btn btn-xs blue btn-outline' href='" . U('Oa/oa_workplan_perf_index', array(
                    'communist_no' => $communist['communist_no']
                )) . "'><i class='fa fa-edit'></i>查看</a>";
                array_push($perf_list,$communist);
            }
        }
        ob_clean();$this->ajaxReturn($perf_list);
    }
    
    // 绩效汇总
    public function report_oaworkplan_perf_info()
    {
        $this->display("Rc/report_oaworkplan_perf_info");
    }
}
