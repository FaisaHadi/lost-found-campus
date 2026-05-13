import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../constants/app_spacing.dart';
import '../../providers/reports_provider.dart';
import '../../routes/route_names.dart';
import '../../utils/view_state.dart';
import '../../widgets/app_text_field.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/error_state.dart';
import '../../widgets/loading_view.dart';
import '../../widgets/report_card.dart';

class ReportListScreen extends StatefulWidget {
  const ReportListScreen({super.key});

  @override
  State<ReportListScreen> createState() => _ReportListScreenState();
}

class _ReportListScreenState extends State<ReportListScreen> {
  final _scrollController = ScrollController();
  final _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _scrollController.addListener(_handleScroll);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final reports = context.read<ReportsProvider>();
      reports.loadCategories();
      reports.loadInitial();
    });
  }

  @override
  void dispose() {
    _scrollController.dispose();
    _searchController.dispose();
    super.dispose();
  }

  void _handleScroll() {
    if (!_scrollController.hasClients) return;
    final position = _scrollController.position;
    if (position.pixels >= position.maxScrollExtent - 240) {
      context.read<ReportsProvider>().loadMore();
    }
  }

  Future<void> _applySearch() {
    final provider = context.read<ReportsProvider>();
    return provider.loadInitial(
      keyword: _searchController.text,
      reportType: provider.reportType,
      categoryId: provider.categoryId,
      status: provider.status,
    );
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<ReportsProvider>(
      builder: (context, reports, child) {
        return Scaffold(
          body: SafeArea(
            top: false,
            child: RefreshIndicator(
              onRefresh: reports.refresh,
              child: ListView(
                controller: _scrollController,
                keyboardDismissBehavior:
                    ScrollViewKeyboardDismissBehavior.onDrag,
                padding: const EdgeInsets.fromLTRB(
                  AppSpacing.screenPadding,
                  AppSpacing.md,
                  AppSpacing.screenPadding,
                  96,
                ),
                children: [
                  _FilterPanel(
                    searchController: _searchController,
                    onSearch: _applySearch,
                  ),
                  const SizedBox(height: AppSpacing.lg),
                  if (reports.state == ViewState.loading)
                    const SizedBox(height: 360, child: LoadingView())
                  else if (reports.state == ViewState.error)
                    SizedBox(
                      height: 360,
                      child: ErrorState(
                        message:
                            reports.errorMessage ??
                            'Laporan tidak dapat dimuat.',
                        onRetry: reports.refresh,
                      ),
                    )
                  else if (reports.state == ViewState.empty)
                    const SizedBox(
                      height: 360,
                      child: EmptyState(
                        title: 'Belum ada laporan',
                        message: 'Coba ubah filter atau buat laporan pertama.',
                        icon: Icons.search_off_rounded,
                      ),
                    )
                  else
                    ..._reportItems(context, reports),
                ],
              ),
            ),
          ),
          floatingActionButton: FloatingActionButton.extended(
            onPressed: () {
              Navigator.of(context).pushNamed(RouteNames.createReport);
            },
            icon: const Icon(Icons.add_a_photo_outlined),
            label: const Text('Laporkan'),
          ),
        );
      },
    );
  }

  List<Widget> _reportItems(BuildContext context, ReportsProvider reports) {
    final items = <Widget>[];
    for (final report in reports.reports) {
      items.add(
        ReportCard(
          report: report,
          onTap: () {
            Navigator.of(
              context,
            ).pushNamed(RouteNames.reportDetail, arguments: report.id);
          },
        ),
      );
      items.add(const SizedBox(height: AppSpacing.md));
    }
    if (reports.isLoadingMore) {
      items.add(
        const Padding(
          padding: EdgeInsets.all(AppSpacing.lg),
          child: Center(child: CircularProgressIndicator()),
        ),
      );
    }
    return items;
  }
}

class _FilterPanel extends StatelessWidget {
  const _FilterPanel({required this.searchController, required this.onSearch});

  final TextEditingController searchController;
  final Future<void> Function() onSearch;

  @override
  Widget build(BuildContext context) {
    final reports = context.watch<ReportsProvider>();

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        AppTextField(
          label: 'Cari laporan',
          controller: searchController,
          textInputAction: TextInputAction.search,
          prefixIcon: const Icon(Icons.search_rounded),
          suffixIcon: IconButton(
            tooltip: 'Cari',
            onPressed: onSearch,
            icon: const Icon(Icons.arrow_forward_rounded),
          ),
          onFieldSubmitted: (_) => onSearch(),
        ),
        const SizedBox(height: AppSpacing.md),
        SingleChildScrollView(
          scrollDirection: Axis.horizontal,
          child: Row(
            children: [
              _TypeChip(label: 'Semua', value: null),
              const SizedBox(width: AppSpacing.sm),
              _TypeChip(label: 'Hilang', value: 'lost'),
              const SizedBox(width: AppSpacing.sm),
              _TypeChip(label: 'Ditemukan', value: 'found'),
            ],
          ),
        ),
        const SizedBox(height: AppSpacing.md),
        DropdownButtonFormField<int?>(
          initialValue: reports.categoryId,
          decoration: const InputDecoration(
            labelText: 'Kategori',
            prefixIcon: Icon(Icons.category_outlined),
          ),
          items: [
            const DropdownMenuItem<int?>(
              value: null,
              child: Text('Semua kategori'),
            ),
            ...reports.categories.map(
              (category) => DropdownMenuItem<int?>(
                value: category.id,
                child: Text(category.name),
              ),
            ),
          ],
          onChanged: (value) {
            context.read<ReportsProvider>().loadInitial(
              keyword: searchController.text,
              reportType: reports.reportType,
              categoryId: value,
              status: reports.status,
            );
          },
        ),
      ],
    );
  }
}

class _TypeChip extends StatelessWidget {
  const _TypeChip({required this.label, required this.value});

  final String label;
  final String? value;

  @override
  Widget build(BuildContext context) {
    final selected = context.select<ReportsProvider, bool>(
      (provider) => provider.reportType == value,
    );

    return ChoiceChip(
      label: Text(label),
      selected: selected,
      onSelected: (_) {
        final reports = context.read<ReportsProvider>();
        reports.loadInitial(
          keyword: reports.keyword,
          reportType: value,
          categoryId: reports.categoryId,
          status: reports.status,
        );
      },
    );
  }
}
