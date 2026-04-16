import React from 'react';

export default function App() {
  return (
    <div className="portal-app">
      <header>
        <div className="logo">JENGA<span>PORTAL</span></div>
        <div className="user-profile">
          <div style={{ textAlign: 'right' }}>
            <div style={{ fontWeight: 600, fontSize: '14px' }}>Jerry Hazoume</div>
            <div style={{ fontSize: '11px', color: 'var(--text-dim)' }}>jerryhazoume@gmail.com</div>
          </div>
          <div className="avatar">JH</div>
        </div>
      </header>
      <div className="container">
        <div className="sidebar-left">
          <div className="welcome-section">
            <h1>Welcome back, <span>Jerry</span></h1>
            <p style={{ fontSize: '13px', color: 'var(--text-dim)' }}>Last portal activity: Today, 2:45 PM</p>
          </div>
          
          <div className="stats-grid">
            <div className="stat-card">
              <div className="stat-label">Active Projects</div>
              <div className="stat-value">03</div>
            </div>
            <div className="stat-card">
              <div className="stat-label">Open Tickets</div>
              <div className="stat-value">02</div>
            </div>
            <div className="stat-card">
              <div className="stat-label">Pending Invoices</div>
              <div className="stat-value">01</div>
            </div>
            <div className="stat-card">
              <div className="stat-label">Days to Launch</div>
              <div className="stat-value">14</div>
            </div>
          </div>

          <div className="section-title">Active Projects <span>View All Projects</span></div>
          
          <div className="project-card">
            <div className="project-header">
              <div className="project-info">
                <div className="project-name">E-Commerce Redesign v2.0</div>
                <div style={{ fontSize: '12px', color: 'var(--text-dim)', marginTop: '4px' }}>Due: Nov 12, 2023</div>
              </div>
              <div className="status-badge status-progress">In Progress</div>
            </div>
            <div className="progress-container">
              <div className="progress-bar" style={{ width: '68%' }}></div>
            </div>
            <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: '11px', color: 'var(--text-dim)' }}>
              <span>68% Complete</span>
              <span>48 of 72 tasks</span>
            </div>
          </div>

          <div className="project-card">
            <div className="project-header">
              <div className="project-info">
                <div className="project-name">API Integration: Stripe Connect</div>
                <div style={{ fontSize: '12px', color: 'var(--text-dim)', marginTop: '4px' }}>Due: Oct 28, 2023</div>
              </div>
              <div className="status-badge status-review">Under Review</div>
            </div>
            <div className="progress-container">
              <div className="progress-bar" style={{ width: '92%' }}></div>
            </div>
            <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: '11px', color: 'var(--text-dim)' }}>
              <span>92% Complete</span>
              <span>Final QA Stage</span>
            </div>
          </div>

          <div className="section-title">Recent Tickets <span>View All Tickets</span></div>
          <div className="ticket-row">
            <div className="ticket-info">
              <div className="ticket-title"><span className="priority-badge priority-high"></span>Header scaling issue on Safari</div>
              <div className="ticket-meta">#89201 • Bug Report • 2 hours ago</div>
            </div>
            <div className="status-badge" style={{ border: '1px solid var(--danger)', color: 'var(--danger)' }}>Awaiting Reply</div>
          </div>
          <div className="ticket-row">
            <div className="ticket-info">
              <div className="ticket-title"><span className="priority-badge priority-medium"></span>Request for brand guidelines access</div>
              <div className="ticket-meta">#89184 • General • Yesterday</div>
            </div>
            <div className="status-badge" style={{ border: '1px solid var(--text-dim)', color: 'var(--text-dim)' }}>Closed</div>
          </div>
        </div>

        <div className="sidebar-right">
          <button className="btn-primary">+ Submit Support Ticket</button>
          
          <div className="section-title">Quick Actions</div>
          <div style={{ background: 'var(--card)', borderRadius: '12px', padding: '8px 20px', border: '1px solid var(--border)', marginBottom: '24px' }}>
            <a href="#" className="action-link">Download Design Assets</a>
            <a href="#" className="action-link">View Contract PDF</a>
            <a href="#" className="action-link">Schedule Meeting</a>
            <a href="#" className="action-link" style={{ border: 'none' }}>Contact Developer</a>
          </div>

          <div className="section-title">Recent Documents</div>
          <div style={{ background: 'var(--card)', borderRadius: '12px', padding: '16px', border: '1px solid var(--border)' }}>
            <div className="doc-item">
              <div className="doc-icon">PDF</div>
              <div style={{ flex: 1 }}>
                <div style={{ fontSize: '13px', fontWeight: 500 }}>Invoice_OCT_23.pdf</div>
                <div style={{ fontSize: '11px', color: 'var(--text-dim)' }}>Billing • 2 days ago</div>
              </div>
            </div>
            <div className="doc-item">
              <div className="doc-icon" style={{ color: '#4ecdc4', background: 'rgba(78,205,196,0.1)' }}>ZIP</div>
              <div style={{ flex: 1 }}>
                <div style={{ fontSize: '13px', fontWeight: 500 }}>Final_Logo_Pack.zip</div>
                <div style={{ fontSize: '11px', color: 'var(--text-dim)' }}>Deliverable • Oct 12</div>
              </div>
            </div>
            <div className="doc-item" style={{ border: 'none' }}>
              <div className="doc-icon" style={{ color: '#ffd166', background: 'rgba(255,209,102,0.1)' }}>DOC</div>
              <div style={{ flex: 1 }}>
                <div style={{ fontSize: '13px', fontWeight: 500 }}>Project_Brief_V2.docx</div>
                <div style={{ fontSize: '11px', color: 'var(--text-dim)' }}>Strategy • Oct 05</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
