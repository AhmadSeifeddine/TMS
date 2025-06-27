<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Task Assignment</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header-icon {
            width: 60px;
            height: 60px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #2d3748;
            margin-bottom: 25px;
            font-weight: 500;
        }
        .task-card {
            background-color: #f7fafc;
            border-left: 5px solid #4299e1;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .task-title {
            font-size: 22px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 15px;
            line-height: 1.3;
        }
        .task-details {
            display: grid;
            gap: 12px;
        }
        .detail-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .detail-label {
            font-weight: 600;
            color: #4a5568;
            min-width: 100px;
        }
        .detail-value {
            color: #2d3748;
            flex: 1;
        }
        .priority-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .priority-high {
            background-color: #fed7d7;
            color: #c53030;
        }
        .priority-medium {
            background-color: #feebc8;
            color: #dd6b20;
        }
        .priority-low {
            background-color: #e6fffa;
            color: #38b2ac;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
            background-color: #bee3f8;
            color: #2b6cb0;
        }
        .description {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            margin-top: 15px;
            color: #4a5568;
            font-style: italic;
        }
        .cta-section {
            text-align: center;
            margin: 30px 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .cta-button:hover {
            transform: translateY(-2px);
        }
        .project-info {
            background-color: #edf2f7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .project-name {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
        }
        .assigned-by {
            color: #718096;
            font-size: 14px;
        }
        .footer {
            background-color: #2d3748;
            color: #a0aec0;
            text-align: center;
            padding: 25px;
            font-size: 14px;
        }
        .footer-logo {
            font-size: 18px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 10px;
        }
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
            margin: 25px 0;
        }
        @media (max-width: 600px) {
            .content {
                padding: 25px 20px;
            }
            .task-card {
                padding: 20px;
            }
            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
            .detail-label {
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="header-icon">
                <svg width="30" height="30" fill="white" viewBox="0 0 24 24">
                    <path d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h1>New Task Assignment</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Hello {{ $assignedUser->name }},
            </div>

            <p style="color: #4a5568; margin-bottom: 25px;">
                You have been assigned a new task that requires your attention. Here are the details:
            </p>

            <!-- Project Info -->
            <div class="project-info">
                <div class="project-name">
                    📁 {{ $project->name }}
                </div>
                <div class="assigned-by">
                    Assigned by {{ $assignedBy->name }}
                </div>
            </div>

            <!-- Task Card -->
            <div class="task-card">
                <div class="task-title">
                    {{ $task->title }}
                </div>

                <div class="task-details">
                    <div class="detail-row">
                        <span class="detail-label">Priority:</span>
                        <span class="detail-value">
                            <span class="priority-badge priority-{{ $task->priority }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value">
                            <span class="status-badge">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Due Date:</span>
                        <span class="detail-value">
                            @if($task->due_date)
                                <strong>{{ $task->due_date->format('F j, Y') }}</strong>
                                <span style="color: #718096; font-size: 14px;">
                                    ({{ $task->due_date->diffForHumans() }})
                                </span>
                            @else
                                <span style="color: #a0aec0;">No due date set</span>
                            @endif
                        </span>
                    </div>
                </div>

                @if($task->description)
                    <div class="description">
                        <strong>Description:</strong><br>
                        {{ $task->description }}
                    </div>
                @endif
            </div>

            <div class="divider"></div>

            <!-- Call to Action -->
            <div class="cta-section">
                <p style="color: #4a5568; margin-bottom: 20px;">
                    Ready to get started? Click the button below to view your task and begin working.
                </p>
                <a href="#" class="cta-button">
                    View Task Details
                </a>
            </div>

            <div class="divider"></div>

            <p style="color: #718096; font-size: 14px; text-align: center; margin: 0;">
                If you have any questions about this task, please reach out to {{ $assignedBy->name }} or your project manager.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-logo">Task Management System</div>
            <div>
                Streamlining productivity, one task at a time.
            </div>
        </div>
    </div>
</body>
</html>
