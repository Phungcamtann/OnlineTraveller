<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\BaseController;
use Core\Request;
use Core\Response;
use App\Services\ScheduleService;
use App\Services\TourService;
use App\Models\Service;
use Exception;

/**
 * Schedule Controller: handles Host departure schedule creation, listing,
 * capacity checks, and schedule cancellations.
 */
class ScheduleController extends BaseController
{
    private ScheduleService $scheduleService;
    private TourService $tourService;
    private Service $serviceModel;

    public function __construct(
        ?Request $request = null,
        ?Response $response = null,
        ?ScheduleService $scheduleService = null,
        ?TourService $tourService = null,
        ?Service $serviceModel = null
    ) {
        parent::__construct($request, $response);
        $this->scheduleService = $scheduleService ?? new ScheduleService();
        $this->tourService = $tourService ?? new TourService();
        $this->serviceModel = $serviceModel ?? new Service();
    }

    /**
     * List all departure schedules for the logged-in Host.
     */
    public function index(Request $request): void
    {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
        }

        $hostId = (int)$user['id'];
        $status = $request->query('status');
        $serviceId = (int)($request->query('service_id') ?? 0);

        $schedules = $this->scheduleService->getHostSchedules($hostId, $status);

        if ($serviceId > 0) {
            $schedules = array_values(array_filter($schedules, fn($s) => (int)$s['service_id'] === $serviceId));
        }

        if ($request->query('format') === 'json') {
            $this->json(['schedules' => $schedules]);
            return;
        }

        $tours = $this->tourService->getHostTours($hostId);

        $this->view('host/schedules/index', [
            'title' => 'Quản Lý Lịch Khởi Hành — Host',
            'schedules' => $schedules,
            'tours' => $tours,
            'selectedServiceId' => $serviceId,
            'status' => $status,
            'user' => $user,
        ]);
    }

    /**
     * Show form to create a new departure schedule.
     */
    public function create(Request $request): void
    {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
        }

        $hostId = (int)$user['id'];
        $serviceId = (int)($request->query('service_id') ?? 0);
        $tours = $this->tourService->getHostTours($hostId);

        $this->view('host/schedules/create', [
            'title' => 'Mở Lịch Khởi Hành Mới — Host',
            'tours' => $tours,
            'selectedServiceId' => $serviceId,
            'user' => $user,
        ]);
    }

    /**
     * Store new departure schedule via ptl_sp_schedules_create.
     */
    public function store(Request $request): void
    {
        $user = $this->user();
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Vui lòng đăng nhập.'], 401);
            return;
        }

        $hostId = (int)$user['id'];
        $serviceId = (int)$request->post('service_id', 0);

        try {
            $schedule = $this->scheduleService->createSchedule($hostId, $serviceId, $request->all());

            if ($request->isAjax() || $request->query('format') === 'json') {
                $this->json([
                    'success' => true,
                    'message' => 'Mở đợt khởi hành mới thành công!',
                    'schedule' => $schedule,
                ]);
                return;
            }

            $this->flash('success', 'Mở lịch khởi hành mới thành công!');
            $this->redirect('/host/schedules');
        } catch (Exception $e) {
            if ($request->isAjax() || $request->query('format') === 'json') {
                $this->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
                return;
            }

            $this->flash('danger', $e->getMessage());
            $this->redirect('/host/schedules/create?service_id=' . $serviceId);
        }
    }

    /**
     * Host cancels an existing departure schedule (ptl_sp_schedules_cancel).
     */
    public function cancel(Request $request, string $id = ''): void
    {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
        }

        $hostId = (int)$user['id'];
        $scheduleId = (int)($id ?: $request->post('schedule_id', 0));
        $reason = (string)$request->post('cancel_reason', '');

        try {
            $schedule = $this->scheduleService->cancelSchedule($hostId, $scheduleId, $reason);

            if ($request->isAjax() || $request->query('format') === 'json') {
                $this->json([
                    'success' => true,
                    'message' => 'Đã hủy lịch khởi hành thành công.',
                    'schedule' => $schedule,
                ]);
                return;
            }

            $this->flash('success', 'Đã hủy lịch khởi hành và tự động hoàn tác các đơn đặt chờ.');
            $this->redirect('/host/schedules');
        } catch (Exception $e) {
            if ($request->isAjax() || $request->query('format') === 'json') {
                $this->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
                return;
            }

            $this->flash('danger', $e->getMessage());
            $this->redirect('/host/schedules');
        }
    }

    /**
     * API: Get available departure dates for a tour (for tourist booking flow).
     */
    public function byService(Request $request, string $id = ''): void
    {
        $serviceId = (int)($id ?: $request->param('id', 0));
        $schedules = $this->scheduleService->getAvailableSchedulesForService($serviceId);

        $this->json([
            'service_id' => $serviceId,
            'total' => count($schedules),
            'schedules' => $schedules,
        ]);
    }
}

if (!class_exists('ScheduleController', false)) {
    class_alias(ScheduleController::class, 'ScheduleController');
}
