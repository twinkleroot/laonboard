<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Lava;
use App\Board;
use App\BoardNew;

class StatusController extends Controller
{
    public function writeStatus(Request $request)
    {
        // 뷰에 필요한 파라미터
        $boards = Board::all();
        $periods = $this->getPeriodList();
        $params = [
            'boards' => $boards,
            'periods' => $periods,
            'selectPeriod' => $request->period,
            'selectBoard' => $request->boardId,
            'selectType' => $request->type,
        ];

        $period = isset($request->period) ? $request->period : '오늘';
        $unit = $periods[$period][0];
        $dataByPeriod = $this->getDataByPeriod($unit);
        $datas = $this->getChartSource($request, $dataByPeriod['query'], $unit);
        if(count($datas) == 0) {
            $params = array_add($params, 'message', '그래프를 만들 데이터가 없습니다.');
            return view('admin.status', $params);
        }
        $chartDataTable = $this->getChartDataTable($datas, $dataByPeriod['pattern'], $period);
        $chartOptions = $this->getChartOptions($unit);
        $renderChart = (!isset($request->type) || $request->type == 'line') ?
                Lava::LineChart('Chart', $chartDataTable, $chartOptions) :
                Lava::ColumnChart('Chart', $chartDataTable, $chartOptions);

        return view('admin.status', $params)->with('chart', $renderChart);
    }

    private function getPeriodList()
    {
        $current = Carbon::now();
        $periods = [
            '오늘' => ['시간'],
            '어제' => ['시간'],
            '7일전' => ['일', Carbon::now()->subDays(7)],
            '14일전' => ['일', Carbon::now()->subDays(14)],
            '1개월전' => ['일', Carbon::now()->subMonth()],
            '3개월전' => ['일', Carbon::now()->subMonths(3)],
            '6개월전' => ['월', Carbon::now()->subMonths(6)],
            '1년전' => ['월', Carbon::now()->subYear()],
            '2년전' => ['월', Carbon::now()->subYears(2)],
            '3년전' => ['년', Carbon::now()->subYears(3)],
            '5년전' => ['년', Carbon::now()->subYears(5)],
            '10년전' => ['년', Carbon::now()->subYears(10)],
        ];

        return $periods;
    }

    // 기간별로 달라지는 쿼리 조각과 날짜 형식의 패턴을 가져온다.
    private function getDataByPeriod($unit)
    {
        $query = '';
        $pattern = '';
        switch ($unit) {
            case '시간':
                $query = 'substr(created_at, 6, 8)';
                $pattern = 'M/d H시';
                break;
            case '일':
                $query = 'substr(created_at, 1, 10)';
                $pattern = 'M/d';
                break;
            case '월':
                $query = 'substr(created_at, 1, 7)';
                $pattern = 'y/M';
                break;
            case '년':
                $query = 'substr(created_at, 1, 4)';
                $pattern = 'y년';
                break;
            default:
                break;
        }

        return [
            'query' => $query,
            'pattern' => $pattern
        ];
    }

    private function getChartSource($request, $query, $unit)
    {
        $period = isset($request->period) ? $request->period : '오늘';
        $boardId = isset($request->boardId) ? $request->boardId : '0';
        // between에 들어가는 from, to를 가져온다.
        $range = $this->getRange($period);
        // 기간별로 통계 데이터를 가져온다.
        $baseDatas = BoardNew::selectRaw($query. ' as at, sum(if(write_id = write_parent, 1, 0)) as write_count, sum(if(write_id = write_parent, 0, 1)) as comment_count')
                ->whereBetween('created_at', [ $range['from'], $range['to'] ]);
        if($boardId > 0) {
            $baseDatas = $baseDatas->where('board_id', $boardId);
        }
        $baseDatas = $baseDatas->groupBy('at')->orderBy('at')->get();
        // 날짜 표시 양식을 통일 - datetime 형식으로 변환
        $baseDatas = $this->convertDateTime($baseDatas, $unit);

        return $baseDatas;
    }

    // between에 들어가는 from, to를 가져온다.
    private function getRange($period)
    {
        if($period == '오늘') {
            $from = Carbon::today();
            $to = Carbon::now();
        } else if($period == '어제') {
            $from = Carbon::yesterday();
            $to = Carbon::yesterday()->setTime(23, 59, 59);
        } else {
            $from = $this->getPeriodList()[$period][1];
            $to = Carbon::yesterday()->setTime(23, 59, 59);
        }

        return [
            'from' => $from,
            'to' => $to
        ];
    }

    private function convertDateTime($baseDatas, $unit)
    {
        foreach($baseDatas as $data) {
            if($unit == '시간') {
                $current = Carbon::now();
                $current->setDate($current->year, substr($data->at, 0, 2), substr($data->at, 3, 2));
                $current->setTime(substr($data->at, 6, 7), 0);
                $data->at = $current->toDateTimeString();
            } else if($unit == '일') {
                $current = Carbon::today();
                $current->setDate($current->year, substr($data->at, 5, 2), substr($data->at, 8, 2));
                $current->setTime(0, 0, 0);
                $data->at = $current->toDateTimeString();
            } else if($unit == '월') {
                $current = Carbon::today();
                $current->setDate(substr($data->at, 0, 4), substr($data->at, 5), $current->day);
                $current->setTime(0, 0, 0);
                $data->at = $current->toDateTimeString();
            } else if($unit == '년') {
                $current = Carbon::today();
                $current->setDate($data->at, $current->month, $current->day);
                $current->setTime(0, 0, 0);
                $data->at = $current->toDateTimeString();
            }
        }

        return $baseDatas;
    }

    // 시점에 따라 날짜 포맷을 다르게 설정한다.
    private function getChartDataTable($datas, $pattern)
    {
        $dateFormat = Lava::DateFormat(['pattern' => $pattern]);
        $chartData = Lava::DataTable();
        $chartData->addDateColumn('Date', $dateFormat)
                ->addNumberColumn('글')
                ->addNumberColumn('댓글');

        foreach($datas as $data) {
            $chartData->addRow([$data->at, $data->write_count, $data->comment_count]);
        }

        return $chartData;
    }

    private function getChartOptions($unit)
    {
        return [
            'title' => '글,댓글 현황',
            'height' => 700,
            'legend' => [
                'position' => 'top'
            ],
            'hAxis' => [
                'title' => $unit
            ],
            'vAxis' => [
                'title' => '글 수',
            ],
            'animation' => [
                'duration' => 3,
                'startup' => true,
                'easing' => 'constant',
            ],
        ];
    }

}
