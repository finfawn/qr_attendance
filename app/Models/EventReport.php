<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'event_id',
        'total_participants',
        'present_count',
        'late_count',
        'absent_count',
        'excused_count',
        'attendance_rate',
        'hourly_check_in_distribution',
        'department_statistics',
        'summary',
        'generated_by'
    ];

    protected $casts = [
        'hourly_check_in_distribution' => 'array',
        'department_statistics' => 'array',
        'attendance_rate' => 'float'
    ];

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    // Helper Methods
    public function getAttendanceStatusSummary()
    {
        return [
            'Present' => $this->present_count,
            'Late' => $this->late_count,
            'Absent' => $this->absent_count,
            'Excused' => $this->excused_count
        ];
    }

    public function getFormattedAttendanceRate()
    {
        return number_format($this->attendance_rate, 2) . '%';
    }

    // Export Methods
    public function toArray()
    {
        return [
            'event_name' => $this->event->title,
            'date_generated' => $this->created_at->format('Y-m-d H:i:s'),
            'total_participants' => $this->total_participants,
            'attendance_statistics' => $this->getAttendanceStatusSummary(),
            'attendance_rate' => $this->getFormattedAttendanceRate(),
            'hourly_distribution' => $this->hourly_check_in_distribution,
            'department_statistics' => $this->department_statistics,
            'summary' => $this->summary,
            'generated_by' => $this->generator->name
        ];
    }

    // Optional: Add method to export as PDF or Excel
    public function exportAsPdf()
    {
        // Implementation for PDF export
        // You'll need to install a PDF package like dompdf
    }

    public function exportAsExcel()
    {
        // Implementation for Excel export
        // You'll need to install a package like maatwebsite/excel
    }
} 