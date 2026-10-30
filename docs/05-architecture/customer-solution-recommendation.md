# Customer solution recommendation

Owner: R04. Reviewers: R17, R20, R25, R29. Status: proposed. Source: prompt. Related: WF-SOFTWARE, OUT-ARCH, AC-09, AC-14. Open: customer evidence/integration contracts.

Every candidate records satisfied, violated and unknown constraints, source answers, reasons, operating/exit costs and validation actions. Unknown hard constraints prevent a definitive choice. Rank feasible candidates without regard to seller margin. Recommendations remain preliminary until material risks are validated.

| Decision | Conditions | Candidate | Alternative assessment |
|---|---|---|---|
| ARCH-CMS | Editorial pages/search/forms, no custom transactions | Supported CMS/hosted content tool | Custom app needs demonstrated mismatch |
| ARCH-COMMERCE | Standard catalog/checkout/inventory/tax/payment fit | Standard commerce tool + verified adapters | Custom checkout only for evidenced mismatch |
| ARCH-MODULAR | Custom stateful workflow/organization policies | Custom modular web app, relational core | Services need measured independent scaling/ownership/legal reason |
| ARCH-MOBILE | Store/device/background/offline capability exceeds verified browser support | Native/cross-platform clients with shared API | PWA ruled out by hard capability, not budget alone |
| ARCH-PWA | Browser/distribution constraints verified; sync policy sufficient | Responsive web/PWA | Store apps require additional benefit |
| ARCH-REALTIME | Peak messages/fan-out/order/latency/recovery quantified | Async/pub-sub around durable core | Total users alone does not justify streaming infrastructure |

Required inputs span goal/build mode, audience, budget/date priorities, surfaces, identity/tenancy, domain features, integrations/migration, workload, quality/geography, content/design and available staff. The software question bank owns exact IDs; the traceability matrix links them to consequences. For web plus apps, count authentication/API/schema/integrations once, then add each UI/device/offline/QA increment. Compare SSR/SPA using interaction, discovery/SEO, payload, accessibility and maintenance capacity. Explain every rejected alternative and the evidence that would reopen it. Sensitive/regulatory unknowns route to specialists.
