<?php

//指导的项目
function query_issue_by_advisor($advisor_id){
	$issueList = D('Common/AdvisorIssue')->getIssueList($advisor_id);
	return $issueList;
}
//关注的项目
 function query_watching_issue_by_advisor($advisor_id){
	$issueList = D('Common/AdvisorWatching')->getWatchingIssueList($advisor_id);
	return $issueList;
}

function query_issue_infomation($issue_id){
	$content = D('Issue/IssueContent')->find($issue_id);
	return $content;
}