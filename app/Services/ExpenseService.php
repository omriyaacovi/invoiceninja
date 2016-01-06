<?php namespace App\Services;

use Utils;
use URL;
use App\Services\BaseService;
use App\Ninja\Repositories\ExpenseRepository;

class ExpenseService extends BaseService
{
    protected $expenseRepo;
    protected $datatableService;

    public function __construct(ExpenseRepository $expenseRepo, DatatableService $datatableService)
    {
        $this->expenseRepo = $expenseRepo;
        $this->datatableService = $datatableService;
    }

    protected function getRepo()
    {
        return $this->expenseRepo;
    }

    public function save($data)
    {
        return $this->expenseRepo->save($data);
    }

    public function getDatatable($vendorPublicId, $search)
    {
        $query = $this->expenseRepo->find($vendorPublicId, $search);

        return $this->createDatatable(ENTITY_CREDIT, $query, !$vendorPublicId);
    }

    protected function getDatatableColumns($entityType, $hideClient)
    {
        return [
            [
                'vendor_name',
                function ($model) {
                    return $model->vendor_public_id ? link_to("vendors/{$model->vendor_public_id}", Utils::getVendorDisplayName($model)) : '';
                },
                ! $hideClient
            ],
            [
                'amount',
                function ($model) {
                    return Utils::formatMoney($model->amount, $model->currency_id, $model->country_id) . '<span '.Utils::getEntityRowClass($model).'/>';
                }
            ],
            [
                'balance',
                function ($model) {
                    return Utils::formatMoney($model->balance, $model->currency_id, $model->country_id);
                }
            ],
            [
                'expense_date',
                function ($model) {
                    return Utils::fromSqlDate($model->expense_date);
                }
            ],
            [
                'private_notes',
                function ($model) {
                    return $model->private_notes;
                }
            ]
        ];
    }

    protected function getDatatableActions($entityType)
    {
        return [
            [
                trans('texts.apply_expense'),
                function ($model) {
                    return URL::to("espense/create/{$model->vendor_public_id}") . '?paymentTypeId=1';
                }
            ]
        ];
    }
}