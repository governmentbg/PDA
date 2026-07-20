@php
    $catColors = [
        'Problem'    => '#dc3545',
        'Suggestion' => '#0d6efd',
        'Praise'     => '#198754',
        'Question'   => '#6f42c1',
    ];
@endphp

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f6f9fc;padding:24px 0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="background:#ffffff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.05);overflow:hidden;">
                <tr>
                    <td style="padding:20px 24px;border-bottom:1px solid #eef1f4;">
                        <h1 style="margin:0;font-size:18px;line-height:1.4;color:#0f172a;">
                            {{ __('feedback.email.title') }}
                        </h1>
                    </td>
                </tr>

                <tr>
                    <td style="padding:20px 24px;">
                        {{-- Summary --}}
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;">
                            <tr>
                                <td style="padding:0 0 10px 0;">
                                    <strong style="color:#0f172a;">{{ __('feedback.email.fields.category') }}:</strong>
                                    <span style="display:inline-block;background:{{ $catColors[$category] ?? '#0d6efd' }};color:#fff;border-radius:9999px;padding:6px 10px;font-size:12px;">
                                      {{ __('feedback.categories.'.$category) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:6px 0;">
                                    <strong style="color:#0f172a;">{{ __('feedback.email.fields.subject') }}:</strong>
                                    <span style="color:#334155;">{{ $subjectLine }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:6px 0;">
                                    <strong style="color:#0f172a;">{{ __('feedback.email.fields.name') }}:</strong>
                                    <span style="color:#334155;">{{ $name }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:6px 0;">
                                    <strong style="color:#0f172a;">{{ __('feedback.email.fields.email') }}:</strong>
                                    <a href="mailto:{{ $contactEmail }}" style="color:#0d6efd;text-decoration:none;">{{ $contactEmail }}</a>
                                </td>
                            </tr>
                        </table>

                        {{-- Description --}}
                        <div style="margin-top:16px;padding-top:12px;border-top:1px solid #eef1f4;">
                            <div style="font-weight:600;color:#0f172a;margin-bottom:8px;">
                                {{ __('feedback.email.fields.description') }}:
                            </div>
                            <div style="color:#334155;line-height:1.6;white-space:pre-wrap;">
                                {!! nl2br(e($description)) !!}
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:14px 24px;border-top:1px solid #eef1f4;color:#64748b;font-size:12px;">
                        {{ config('app.name') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
