<style>
.site-footer {
    width: 100%;
    background: #ffffff;
    background: linear-gradient(135deg, #171718, #0c0c0e);
    padding: 32px;
    margin-top: 48px;
}

.footer-inner {
    max-width: 1100px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 32px;
}

.footer-left a {
    text-decoration: none;
    font-weight: 600;
    color: #dd853dff;
}

.footer-left a:hover {
    color: #dd3d3dff;
    text-decoration: underline;
}


.footer-right h2 {
    color: #dd853dff;
    font-size: 22px;
    margin-bottom: 8px;
}

.footer-right p {
    font-size: 14px;
    color: #555;
    line-height: 1.6;
}


.footer-bottom {
    text-align: center;
    margin-top: 32px;
    font-size: 13px;
    color: #777;
}

.complaint-footer a {
    color: #4f6ef7;          
    font-weight: 600;
    text-decoration: underline;
}

.complaint-footer a:hover {
    color: #6f86ff;
}

.support-email {
    font-size: 14px;
    letter-spacing: 0.3px;
}

.support-email a {
    color: #dd853dff;
}

@media (max-width: 768px) {
    .footer-inner {
        flex-direction: column;
        gap: 20px;
    }
}
</style>

<footer class="site-footer">
    <div class="footer-inner">
       
        <div class="footer-left">
            <a href="#top">↑ Back to top</a>
        </div>
        <div class="footer-right">
            <h2>ANY issues?</h2>
            <p class="support-email">
            Email: <a href="mailto:support@example.com">support@example.com</a>
            </p>
            <p>Phone: +880 1XXX-XXXXXX</p>
        </div>
    </div>

    <div class="footer-bottom">
        © <?php echo date('Y'); ?> One Tap Bus. All rights reserved.
    </div>
</footer>

</body>
</html>
