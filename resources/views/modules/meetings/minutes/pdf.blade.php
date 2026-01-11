<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Meeting Minutes - {{ $meeting->title }}</title>
    <style>
        @page { margin: 20px 30px 60px 30px; }
        body { font-family: "Helvetica", Arial, sans-serif; font-size: 9pt; color: #333; line-height: 1.4; }
        h1, h2, h3 { color: #500000; margin: 0 0 10px 0; font-weight: bold; }
        h1 { font-size: 20pt; border-bottom: 2px solid #940000; padding-bottom: 10px; margin-bottom: 20px; text-align: center; }
        h2 { font-size: 14pt; background-color: #fceeee; padding: 10px; margin-top: 20px; border-left: 4px solid #940000; }
        h3 { font-size: 12pt; margin-top: 15px; margin-bottom: 10px; }
        h4 { font-size: 11pt; margin-top: 12px; margin-bottom: 8px; }
        h5 { font-size: 10pt; margin-top: 10px; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 6px; text-align: left; vertical-align: top; border: 1px solid #ddd; }
        th { background-color: #f9f9f9; font-weight: bold; font-size: 8pt; }
        .agenda-section { page-break-inside: avoid; margin-bottom: 20px; padding: 10px; background-color: #f8f9fa; border-left: 3px solid #28a745; }
        .action-item-section { page-break-inside: avoid; margin-bottom: 15px; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 8px; color: white; font-weight: bold; font-size: 8pt; }
        .badge-info { background-color: #17a2b8; }
        .signature-section { margin-top: 40px; padding-top: 20px; border-top: 2px solid #ddd; }
        .signature-box { width: 45%; display: inline-block; vertical-align: top; }
        ol, ul { margin: 5px 0; padding-left: 20px; }
        li { margin-bottom: 5px; }
        p { margin: 5px 0; }
    </style>
</head>
<body>
    @php
        $orgSettings = \App\Models\OrganizationSetting::getSettings();
        $timezone = $orgSettings->timezone ?? config('app.timezone', 'Africa/Dar_es_Salaam');
        $documentDate = \Carbon\Carbon::parse($meeting->meeting_date)->setTimezone($timezone)->format($orgSettings->date_format ?? 'd M Y');
        $documentRef = 'MEETING-MINUTES-' . $meeting->reference_code ?? $meeting->id . '-' . now()->setTimezone($timezone)->format('YmdHis');
    @endphp
    
    @include('components.pdf-header', [
        'documentTitle' => 'MEETING MINUTES',
        'documentRef' => $documentRef,
        'documentDate' => $documentDate
    ])

    <main>
        <h1>Meeting Minutes</h1>
        <h2 style="text-align: center; margin-bottom: 20px;">{{ $meeting->title }}</h2>
        
        <!-- Meeting Details -->
        <table>
            <tr>
                <th style="width: 20%;">Date</th>
                <td>{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('l, d F Y') }}</td>
                <th style="width: 20%;">Time</th>
                <td>{{ $meeting->start_time }} - {{ $meeting->end_time }}</td>
            </tr>
            <tr>
                <th>Venue</th>
                <td colspan="3">{{ $meeting->venue ?? $meeting->location ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Category</th>
                <td colspan="3">{{ $meeting->category_name ?? 'N/A' }}</td>
            </tr>
            @if($meeting->branch_name)
            <tr>
                <th>Branch</th>
                <td colspan="3">{{ $meeting->branch_name }} @if($meeting->branch_code)({{ $meeting->branch_code }})@endif</td>
            </tr>
            @endif
            @if($meeting->creator_name)
            <tr>
                <th>Organized By</th>
                <td colspan="3">{{ $meeting->creator_name }}</td>
            </tr>
            @endif
        </table>

        <!-- Attendance -->
        <h3>ATTENDANCE</h3>
        <div>
            <strong>Present:</strong>
            <ol>
                @forelse($attendees as $attendee)
                    <li>
                        {{ $attendee->user_name ?? $attendee->name }}
                        @if($attendee->participant_type == 'external')
                            <span class="badge badge-info">External</span>
                        @endif
                    </li>
                @empty
                    <li>No attendees recorded</li>
                @endforelse
            </ol>
        </div>

        <!-- Agenda Discussions -->
        @if($agendas->count() > 0)
        <h3>AGENDA DISCUSSIONS</h3>
        @foreach($agendas as $index => $agenda)
        <div class="agenda-section">
            <h4>{{ $index + 1 }}. {{ $agenda->title }}</h4>
            @if($agenda->presenter_name)
                <p><em>Presented by: {{ $agenda->presenter_name }}</em></p>
            @endif
            @if($agenda->discussion_notes)
                <p><strong>Discussion:</strong><br>{!! nl2br(e($agenda->discussion_notes)) !!}</p>
            @endif
            @if($agenda->resolution)
                <p><strong>Resolution:</strong><br>{!! nl2br(e($agenda->resolution)) !!}</p>
            @endif
        </div>
        @endforeach
        @endif

        <!-- Action Items -->
        @if($actionItems && $actionItems->count() > 0)
        <h3>ACTION ITEMS</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 40%;">Action</th>
                    <th style="width: 25%;">Responsible</th>
                    <th style="width: 15%;">Deadline</th>
                    <th style="width: 15%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($actionItems as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->title ?? $item->description ?? 'N/A' }}</td>
                    <td>{{ $item->responsible_name ?? 'TBD' }}</td>
                    <td>{{ $item->deadline ? \Carbon\Carbon::parse($item->deadline)->format('d M Y') : 'TBD' }}</td>
                    <td>
                        @php
                            $status = $item->status ?? 'open';
                            $statusColors = [
                                'open' => '#6c757d',
                                'in_progress' => '#17a2b8',
                                'done' => '#28a745',
                                'completed' => '#28a745'
                            ];
                            $statusColor = $statusColors[$status] ?? '#6c757d';
                        @endphp
                        <span class="badge" style="background-color: {{ $statusColor }};">
                            {{ ucwords(str_replace('_', ' ', $status)) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- Any Other Business -->
        @if($minutes && $minutes->aob)
        <h3>ANY OTHER BUSINESS</h3>
        <p>{!! nl2br(e($minutes->aob)) !!}</p>
        @endif

        <!-- Next Meeting -->
        @if($minutes && $minutes->next_meeting_date)
        <h3>NEXT MEETING</h3>
        <table>
            <tr>
                <th style="width: 20%;">Date</th>
                <td>{{ \Carbon\Carbon::parse($minutes->next_meeting_date)->format('l, d F Y') }}</td>
            </tr>
            @if($minutes->next_meeting_time)
            <tr>
                <th>Time</th>
                <td>{{ $minutes->next_meeting_time }}</td>
            </tr>
            @endif
            @if($minutes->next_meeting_venue)
            <tr>
                <th>Venue</th>
                <td>{{ $minutes->next_meeting_venue }}</td>
            </tr>
            @endif
        </table>
        @endif

        <!-- Meeting Closed -->
        @if($minutes && $minutes->closing_time)
        <h3>MEETING CLOSED</h3>
        <p>
            The meeting was closed at {{ $minutes->closing_time }}.
            @if($minutes->closing_remarks)
                <br>{!! nl2br(e($minutes->closing_remarks)) !!}
            @endif
        </p>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <p><strong>Minutes Prepared By:</strong></p>
                @if(isset($preparedByUser) && $preparedByUser)
                    <p style="margin-top: 20px; font-weight: bold;">{{ $preparedByUser->name }}</p>
                    <p style="margin-top: 20px;">_________________________</p>
                    <p><small>Signature</small></p>
                @else
                    <p style="margin-top: 40px;">_________________________</p>
                    <p><small>Name & Signature</small></p>
                @endif
            </div>
            <div class="signature-box" style="margin-left: 5%;">
                <p><strong>Approved By:</strong></p>
                @if(isset($approvedByUser) && $approvedByUser)
                    <p style="margin-top: 20px; font-weight: bold;">{{ $approvedByUser->name }}</p>
                    @if($minutes && $minutes->approved_at)
                        <p style="margin-top: 5px; font-size: 8pt; color: #666;">Date: {{ \Carbon\Carbon::parse($minutes->approved_at)->format('d M Y') }}</p>
                    @endif
                    <p style="margin-top: 20px;">_________________________</p>
                    <p><small>Signature</small></p>
                @else
                    <p style="margin-top: 40px;">_________________________</p>
                    <p><small>Chairperson Signature</small></p>
                @endif
            </div>
        </div>
    </main>
</body>
</html>

